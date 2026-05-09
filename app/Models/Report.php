<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'latitude',
        'longitude',
        'location_name',
        'photo_path',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'latitude'   => 'float',
        'longitude'  => 'float',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function votes()
    {
        return $this->hasMany(ReportVote::class);
    }

    public function userVote()
    {
        return $this->hasOne(ReportVote::class)->where('user_id', auth()->id());
    }

    public function getTypeIconAttribute()
    {
        return match ($this->type) {
            'macet'       => '🚨',
            'kecelakaan'  => '🚑',
            'jalan_rusak' => '⚠️',
            default       => 'ℹ️',
        };
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'macet'       => 'Kemacetan',
            'kecelakaan'  => 'Kecelakaan',
            'jalan_rusak' => 'Jalan Rusak',
            default       => 'Lainnya',
        };
    }

    public function getPhotoUrlAttribute()
    {
        if (!$this->photo_path || $this->photo_path === '0') return null;
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk(config('filesystems.default'));
        return $disk->url($this->photo_path);
    }

    public function getMacetVotesAttribute()
    {
        return $this->votes->where('vote_type', 'macet')->count();
    }

    public function getPadatVotesAttribute()
    {
        return $this->votes->where('vote_type', 'padat')->count();
    }

    public function getLancarVotesAttribute()
    {
        return $this->votes->where('vote_type', 'lancar')->count();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function checkAndExpire()
    {
        if ($this->expires_at && $this->expires_at->isPast() && $this->status === 'active') {
            $this->update(['status' => 'expired']);
        }
    }
}
