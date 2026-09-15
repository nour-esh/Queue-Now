<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Validation\ValidationException;

class TicketStateMachine
{
    /**
     * Defines which status a ticket is allowed to move to, from each current status.
     */
    private const ALLOWED_TRANSITIONS = [
        'WAITING'   => ['CALLED', 'CANCELLED'],
        'CALLED'    => ['SERVING', 'SKIPPED'],
        'SERVING'   => ['DONE'],
        'DONE'      => [],
        'SKIPPED'   => [],
        'CANCELLED' => [],
    ];

    /**
     * Checks whether a transition is allowed, without changing anything.
     */
    public function canTransition(Ticket $ticket, string $toStatus): bool
    {
        return in_array($toStatus, self::ALLOWED_TRANSITIONS[$ticket->status] ?? []);
    }

    /**
     * Attempts to move a ticket to a new status.
     * Throws a ValidationException (mapped to 409 by the controller) if not allowed.
     */
    public function transition(Ticket $ticket, string $toStatus): Ticket
    {
        if (! $this->canTransition($ticket, $toStatus)) {
            throw ValidationException::withMessages([
                'status' => "Cannot move ticket from {$ticket->status} to {$toStatus}.",
            ]);
        }

        $ticket->status = $toStatus;

        // Stamp the correct timestamp column for this transition
        match ($toStatus) {
            'CALLED'    => $ticket->called_at = now(),
            'SERVING'   => $ticket->started_at = now(),
            'DONE'      => $ticket->finished_at = now(),
            'SKIPPED'   => $ticket->skipped_at = now(),
            'CANCELLED' => $ticket->cancelled_at = now(),
            default     => null,
        };

        $ticket->save();

        return $ticket;
    }
}