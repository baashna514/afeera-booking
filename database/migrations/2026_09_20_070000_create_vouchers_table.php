<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->date('booking_date');
            $table->string('terminal_city');
            $table->integer('total_seats_issued')->default(0);
            $table->decimal('total_revenue', 10, 2)->default(0.00);
            $table->decimal('total_expenses', 10, 2)->default(0.00);
            $table->decimal('driver_allowance', 10, 2)->default(0.00);
            $table->decimal('guard_allowance', 10, 2)->default(0.00);
            $table->decimal('hostess_allowance', 10, 2)->default(0.00);
            $table->decimal('total_staff_expenses', 10, 2)->default(0.00);
            $table->decimal('net_amount', 10, 2)->default(0.00);
            $table->boolean('is_final_voucher')->default(false);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('voucher_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained('vouchers')->cascadeOnDelete();
            $table->foreignId('terminal_expense_type_id')->nullable()->constrained('terminal_expense_types')->nullOnDelete();
            $table->string('title');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_expenses');
        Schema::dropIfExists('vouchers');
    }
};
