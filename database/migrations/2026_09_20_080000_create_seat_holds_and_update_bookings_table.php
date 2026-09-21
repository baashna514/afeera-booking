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
        // 1. Seat Holds / Locks table (temporary hold to prevent double booking across counters)
        Schema::create('seat_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->date('booking_date');
            $table->integer('seat_number');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('session_id')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->unique(['schedule_id', 'booking_date', 'seat_number']);
        });

        // 2. Enhance bookings table with phone, cnic and reservation status
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('passenger_name');
            $table->string('cnic')->nullable()->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'cnic']);
        });

        Schema::dropIfExists('seat_holds');
    }
};
