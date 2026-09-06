<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $fillable = ['name', 'service_id', 'role'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}