<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = ['service_id', 'ticket_number', 'queue_date', 'status'];

    protected $casts = [
        'queue_date'   => 'date',
        'called_at'    => 'datetime',
        'started_at'   => 'datetime',
        'finished_at'  => 'datetime',
        'skipped_at'   => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}