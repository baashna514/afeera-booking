<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'vehicle_service_type_id',
        'vehicle_id',
        'departure_time',
        'arrival_time',
        'duration_minutes',
        'status',
    ];

    /**
     * The route this schedule belongs to.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * The vehicle service type for this schedule.
     */
    public function vehicleServiceType(): BelongsTo
    {
        return $this->belongsTo(VehicleServiceType::class);
    }

    /**
     * The specific bus vehicle assigned to this schedule.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Bookings for this schedule.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
