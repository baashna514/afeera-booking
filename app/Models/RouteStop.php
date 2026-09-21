<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'city_id',
        'stop_order',
        'distance_from_origin_km',
        'duration_from_origin_minutes',
    ];

    /**
     * The route this stop belongs to.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * The city at this stop.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
