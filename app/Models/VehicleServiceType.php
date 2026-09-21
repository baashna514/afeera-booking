<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'total_seats',
        'description',
        'status',
    ];

    /**
     * Vehicles of this service type.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Schedules using this vehicle service type.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Fares associated with this vehicle service type.
     */
    public function fares(): HasMany
    {
        return $this->hasMany(Fare::class);
    }
}
