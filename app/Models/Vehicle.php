<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_number',
        'registration_number',
        'company_id',
        'vehicle_service_type_id',
        'status',
    ];

    /**
     * The company that owns/operates this bus vehicle.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * The vehicle service type (Gold Class, Business, Economy).
     */
    public function vehicleServiceType(): BelongsTo
    {
        return $this->belongsTo(VehicleServiceType::class);
    }

    /**
     * Schedules assigned to this vehicle.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
