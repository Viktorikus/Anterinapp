<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'trayek_code', 'trayek_name', 'type',
        'status', 'capacity', 'plate_number', 'driver_name', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function positions()
    {
        return $this->hasMany(VehiclePosition::class);
    }

    public function latestPosition()
    {
        return $this->hasOne(VehiclePosition::class)->latestOfMany();
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'berangkat'    => 'Berangkat',
            'berhenti'     => 'Berhenti',
            'menuju_halte' => 'Menuju Halte',
            default        => 'Tidak Diketahui',
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'berangkat'    => '#00C9A7',
            'berhenti'     => '#FF6B6B',
            'menuju_halte' => '#FFD93D',
            default        => '#6B7280',
        };
    }
}
