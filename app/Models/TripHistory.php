<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'bookmark_id',
        'origin_name', 'origin_lat', 'origin_lng',
        'destination_name', 'destination_lat', 'destination_lng',
        'route_taken',
    ];

    protected $casts = [
        'origin_lat'      => 'float',
        'origin_lng'      => 'float',
        'destination_lat' => 'float',
        'destination_lng' => 'float',
        'route_taken'     => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookmark()
    {
        return $this->belongsTo(Bookmark::class);
    }
}
