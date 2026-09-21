<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeatHold extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'booking_date',
        'seat_number',
        'user_id',
        'session_id',
        'expires_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'expires_at' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if hold is currently active.
     */
    public function isActive(): bool
    {
        return $this->expires_at->isFuture();
    }
}
