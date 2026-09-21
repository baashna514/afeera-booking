<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_number',
        'schedule_id',
        'booking_date',
        'terminal_city',
        'total_seats_issued',
        'total_revenue',
        'total_expenses',
        'driver_allowance',
        'guard_allowance',
        'hostess_allowance',
        'total_staff_expenses',
        'net_amount',
        'is_final_voucher',
        'created_by',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'is_final_voucher' => 'boolean',
        'total_revenue' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'driver_allowance' => 'decimal:2',
        'guard_allowance' => 'decimal:2',
        'hostess_allowance' => 'decimal:2',
        'total_staff_expenses' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(VoucherExpense::class);
    }
}
