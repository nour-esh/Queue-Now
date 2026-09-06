<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['name', 'is_open'];
    protected $casts = ['is_open' => 'boolean'];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}