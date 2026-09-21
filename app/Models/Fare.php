<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fare extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'from_city_id',
        'to_city_id',
        'vehicle_service_type_id',
        'fare_amount',
        'status',
    ];

    /**
     * The route this fare belongs to.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * The boarding city.
     */
    public function fromCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    /**
     * The destination city.
     */
    public function toCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    /**
     * The vehicle service type for this fare.
     */
    public function vehicleServiceType(): BelongsTo
    {
        return $this->belongsTo(VehicleServiceType::class);
    }
}
