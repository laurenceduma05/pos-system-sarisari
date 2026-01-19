<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'by',
        'details',
        'link',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Scope for upcoming events
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>=', now());
    }

    // Scope for past events
    public function scopePast($query)
    {
        return $query->where('end_time', '<', now());
    }

    // Scope for current events (ongoing)
    public function scopeCurrent($query)
    {
        return $query->where('start_time', '<=', now())
                    ->where('end_time', '>=', now());
    }
}
