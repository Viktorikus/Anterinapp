<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteStop extends Model
{
    use HasFactory;

    protected $fillable = [
        'transit_route_id', 'name', 'latitude', 'longitude', 'order_number',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function route()
    {
        return $this->belongsTo(TransitRoute::class, 'transit_route_id');
    }
}
