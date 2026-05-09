<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'transit_route_id', 'departure_time', 'arrival_time', 'days_of_week', 'notes',
    ];

    protected $casts = [
        'days_of_week' => 'array',
    ];

    public function route()
    {
        return $this->belongsTo(TransitRoute::class, 'transit_route_id');
    }

    public function getDaysLabelAttribute()
    {
        if (!$this->days_of_week) return 'Setiap Hari';
        $map = ['Mon'=>'Sen','Tue'=>'Sel','Wed'=>'Rab','Thu'=>'Kam','Fri'=>'Jum','Sat'=>'Sab','Sun'=>'Min'];
        return implode(', ', array_map(fn($d) => $map[$d] ?? $d, $this->days_of_week));
    }
}
