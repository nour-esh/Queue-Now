<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        return DB::transaction(function () use ($request) {
            // Lock and calculate next ticket number for the service for today
            $lastTicket = Ticket::where('service_id', $request->service_id)
                ->whereDate('created_at', now()->toDateString())
                ->lockForUpdate()
                ->latest('id')
                ->first();

            $nextNumber = $lastTicket ? $lastTicket->ticket_number + 1 : 1;

            $ticket = Ticket::create([
                'service_id' => $request->service_id,
                'ticket_number' => $nextNumber,
                'status' => 'WAITING', // Standardized uppercase status
            ]);

            return response()->json($ticket, 201);
        });
    }

    /**
     * Display the specified ticket status.
     */
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);

        $peopleBefore = Ticket::where('service_id', $ticket->service_id)
            ->where('status', 'WAITING')
            ->where('id', '<', $ticket->id)
            ->count();

        return response()->json([
            'ticket' => $ticket,
            'people_before' => $peopleBefore,
        ]);
    }

    /**
     * Cancel an active ticket.
     */
    public function cancel($id)
    {
        $ticket = Ticket::findOrFail($id);

        if (in_array($ticket->status, ['DONE', 'CANCELLED'])) {
            return response()->json([
                'message' => 'Cannot cancel a ticket that is already completed or cancelled.'
            ], 400);
        }

        $ticket->update([
            'status' => 'CANCELLED',
            'cancelled_at' => now(), // Standardized timestamp
        ]);

        return response()->json([
            'message' => 'Ticket cancelled successfully',
            'ticket' => $ticket
        ]);
    }
}