<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransitRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'color', 'description',
        'start_point', 'end_point', 'distance_km', 'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'distance_km' => 'float',
    ];

    public function stops()
    {
        return $this->hasMany(RouteStop::class)->orderBy('order_number');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class)->orderBy('departure_time');
    }
}
