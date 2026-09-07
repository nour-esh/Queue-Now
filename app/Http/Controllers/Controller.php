<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\Service;
use App\Models\ServiceDailyCounter;
use Carbon\Carbon;

class TicketController extends Controller
{
    // 1. Take a new ticket
    public function store(Request $request)
    {
        // Check that the request sent a service_id
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $serviceId = $request->input('service_id');
        $today = Carbon::today()->toDateString();

        // Check if the service/clinic department is open
        $service = DB::table('services')->where('id', $serviceId)->first();
        if (!$service || !$service->is_open) {
            return response()->json(['error' => 'This service is currently closed.'], 400);
        }

        // Use a transaction + lock so two people tapping at the exact same millisecond never get the same number
        $result = DB::transaction(function () use ($serviceId, $today) {
            // Find or create today's ticket counter for this service, locked for update
            $counter = DB::table('service_daily_counters')
                ->where('service_id', $serviceId)
                ->where('queue_date', $today)
                ->lockForUpdate()
                ->first();

            if (!$counter) {
                // First ticket of the day starts at 1
                $nextNumber = 1;
                DB::table('service_daily_counters')->insert([
                    'service_id' => $serviceId,
                    'queue_date' => $today,
                    'last_number' => 1,
                ]);
            } else {
                // Otherwise increment the last number by 1
                $nextNumber = $counter->last_number + 1;
                DB::table('service_daily_counters')
                    ->where('id', $counter->id)
                    ->update(['last_number' => $nextNumber]);
            }

            // Create the new ticket in the database
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

        // Count how many waiting tickets are ahead of this new one
        $peopleAhead = DB::table('tickets')
            ->where('service_id', $serviceId)
            ->where('queue_date', $today)
            ->where('status', 'WAITING')
            ->where('id', '<', $result['ticket_id'])
            ->count();

        return response()->json([
            'message' => 'Ticket created successfully!',
            'ticket_id' => $result['ticket_id'],
            'ticket_number' => $result['ticket_number'],
            'status' => 'WAITING',
            'people_ahead' => $peopleAhead,
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

        // Patient can only cancel if ticket is still WAITING
        if ($ticket->status !== 'WAITING') {
            return response()->json([
                'error' => 'Cannot cancel ticket because it is already called or processed.'
            ], 400);
        }

        // Update status to CANCELLED and stamp cancelled_at time
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
}