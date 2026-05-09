<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id', 'title', 'content', 'type',
        'is_active', 'starts_at', 'expires_at',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'closure'      => '🚫',
            'event'        => '🎉',
            'route_change' => '🔄',
            'repair'       => '🔧',
            default        => 'ℹ️',
        };
    }

    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'closure'      => 'Penutupan Jalan',
            'event'        => 'Event Kota',
            'route_change' => 'Perubahan Rute',
            'repair'       => 'Perbaikan Jalan',
            default        => 'Informasi',
        };
    }

    public function getTypeBadgeColorAttribute()
    {
        return match($this->type) {
            'closure'      => '#FF6B6B',
            'event'        => '#FFD93D',
            'route_change' => '#6BCB77',
            'repair'       => '#4D96FF',
            default        => '#00C9A7',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }
}
