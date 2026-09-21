<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'province',
        'status',
    ];

    /**
     * Routes that originate from this city.
     */
    public function originRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'origin_city_id');
    }

    /**
     * Routes that end at this city.
     */
    public function destinationRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'destination_city_id');
    }

    /**
     * Route stops where this city is an intermediate stop.
     */
    public function routeStops(): HasMany
    {
        return $this->hasMany(RouteStop::class);
    }
}
