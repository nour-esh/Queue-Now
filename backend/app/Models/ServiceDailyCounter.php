<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceDailyCounter extends Model
{
    protected $fillable = ['service_id', 'queue_date', 'last_number'];

    protected $casts = [
        'queue_date' => 'date',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}