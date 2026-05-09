<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name',
        'origin_name', 'origin_lat', 'origin_lng',
        'destination_name', 'destination_lat', 'destination_lng',
        'use_count',
    ];

    protected $casts = [
        'origin_lat'      => 'float',
        'origin_lng'      => 'float',
        'destination_lat' => 'float',
        'destination_lng' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function histories()
    {
        return $this->hasMany(TripHistory::class);
    }
}
