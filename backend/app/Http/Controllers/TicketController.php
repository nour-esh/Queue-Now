<?php

namespace App\Http\Controllers;


use App\Services\TicketStateMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\Service;
use App\Services\AiPredictionService;
use Carbon\Carbon;

class TicketController extends Controller
{
    protected AiPredictionService $aiService;

    public function __construct(AiPredictionService $aiService)
    {
        $this->aiService = $aiService;
    }
    // 1. Take a new ticket
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $serviceId = $request->input('service_id');
        $today = Carbon::today()->toDateString();

        $service = DB::table('services')->where('id', $serviceId)->first();
        if (!$service || !$service->is_open) {
            return response()->json(['error' => 'This service is currently closed.'], 400);
        }

        $result = DB::transaction(function () use ($serviceId, $today) {
            // Atomically ensure a counter row exists for this service+day, without racing
            DB::table('service_daily_counters')->insertOrIgnore([
                'service_id' => $serviceId,
                'queue_date' => $today,
                'last_number' => 0,
            ]);

            $counter = DB::table('service_daily_counters')
                ->where('service_id', $serviceId)
                ->where('queue_date', $today)
                ->lockForUpdate()
                ->first();

            $nextNumber = $counter->last_number + 1;

            DB::table('service_daily_counters')
                ->where('id', $counter->id)
                ->update(['last_number' => $nextNumber]);

            $ticketId = DB::table('tickets')->insertGetId([
                'service_id' => $serviceId,
                'ticket_number' => $nextNumber,
                'queue_date' => $today,
                'status' => 'WAITING',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return [
                'ticket_id' => $ticketId,
                'ticket_number' => $nextNumber,
            ];
        });

        $peopleAhead = DB::table('tickets')
            ->where('service_id', $serviceId)
            ->where('queue_date', $today)
            ->where('status', 'WAITING')
            ->where('id', '<', $result['ticket_id'])
            ->count();

        $lastDoneTicket = DB::table('tickets')
            ->where('service_id', $serviceId)
            ->where('status', 'DONE')
            ->latest('updated_at')
            ->first();

        $previousWaitingTime = 10.0; // default baseline minutes
        if ($lastDoneTicket && $lastDoneTicket->created_at && $lastDoneTicket->updated_at) {
            $previousWaitingTime = max(1.0, \Carbon\Carbon::parse($lastDoneTicket->created_at)->diffInMinutes($lastDoneTicket->updated_at));
        }

        $payload = [
            'queue_length_before'   => $peopleAhead,
            'avg_service_time'      => $service->avg_service_time ?? 10.0, 
            'available_staff'       => 2, // Replace with dynamic staff query if available in your DB
            'previous_waiting_time' => $previousWaitingTime,
            'hour'                  => now()->hour,          // Current hour (0-23)
            'day_of_week'           => now()->dayOfWeek,     // Current day (0-6)
            'service_type'          => $service->type ?? 'checkup', // Must match: "consultation", "checkup", "lab_test", "payment"
        ];
        $estimatedMinutes= $this->aiService->getEstimatedWaitingTime($payload) ?? 15.0;

        return response()->json([
            'message' => 'Ticket created successfully!',
            'ticket_id' => $result['ticket_id'],
            'ticket_number' => $result['ticket_number'],
            'status' => 'WAITING',
            'people_ahead' => $peopleAhead,
            'estimated_waiting_time_minutes' => $estimatedMinutes
        ], 201);
    }

    // 2. Patient checks their ticket status
    public function show($id)
    {
        $ticket = DB::table('tickets')->where('id', $id)->first();

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found.'], 404);
        }

        return response()->json([
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'status' => $ticket->status,
            'created_at' => $ticket->created_at,
        ]);
    }

    // 3. Patient cancels their ticket
    public function cancel($id)
    {
        $ticket = DB::table('tickets')->where('id', $id)->first();

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found.'], 404);
        }

        if ($ticket->status !== 'WAITING') {
            return response()->json([
                'error' => 'Cannot cancel ticket because it is already called or processed.'
            ], 400);
        }

        DB::table('tickets')
            ->where('id', $id)
            ->update([
                'status' => 'CANCELLED',
                'cancelled_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Ticket successfully cancelled.',
            'status' => 'CANCELLED',
        ]);
    }

    // 4. Employee views the waiting list for their service
    public function waitingList(Request $request, $serviceId)
    {
        $service = Service::find($serviceId);

        if (!$service) {
            return response()->json([
                'message' => 'Service not found.'
            ], 404);
        }

        $tickets = Ticket::where('service_id', $serviceId)
            ->where('status', 'WAITING')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($tickets->isEmpty()) {
            return response()->json([
                'message' => 'No one is currently waiting for this service.',
                'tickets' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Waiting list retrieved successfully.',
            'tickets' => $tickets
        ], 200);
    }
        // 5. Employee calls the next WAITING ticket for their service
    public function next(Request $request, TicketStateMachine $stateMachine, $serviceId)
    {
        $ticket = DB::transaction(function () use ($serviceId, $stateMachine) {
            $ticket = Ticket::where('service_id', $serviceId)
                ->where('status', 'WAITING')
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->first();

            if (! $ticket) {
                return null;
            }

            return $stateMachine->transition($ticket, 'CALLED');
        });

        if (! $ticket) {
            return response()->json([
                'message' => 'No one is currently waiting for this service.',
            ], 404);
        }

        return response()->json([
            'message' => 'Ticket called successfully.',
            'ticket'  => $ticket,
        ], 200);
    }

    // 6. Employee starts serving a called ticket
    public function start(TicketStateMachine $stateMachine, $id)
    {
        return $this->applyTransition($stateMachine, $id, 'SERVING', 'Ticket service started.');
    }

    // 7. Employee marks a ticket as done
    public function done(TicketStateMachine $stateMachine, $id)
    {
        return $this->applyTransition($stateMachine, $id, 'DONE', 'Ticket completed.');
    }

    // 8. Employee skips a called ticket
    public function skip(TicketStateMachine $stateMachine, $id)
    {
        return $this->applyTransition($stateMachine, $id, 'SKIPPED', 'Ticket skipped.');
    }

    /**
     * Shared helper for start/done/skip: find the ticket, attempt the transition,
     * and return a consistent response shape.
     */
    private function applyTransition(TicketStateMachine $stateMachine, $id, string $toStatus, string $successMessage)
    {
        $ticket = Ticket::find($id);

        if (! $ticket) {
            return response()->json(['error' => 'Ticket not found.'], 404);
        }

        try {
            $stateMachine->transition($ticket, $toStatus);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->validator->errors()->first(),
            ], 409);
        }

        return response()->json([
            'message' => $successMessage,
            'ticket'  => $ticket,
        ], 200);
    }
}