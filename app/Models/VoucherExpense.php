<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_id',
        'terminal_expense_type_id',
        'title',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function terminalExpenseType(): BelongsTo
    {
        return $this->belongsTo(TerminalExpenseType::class);
    }
}
