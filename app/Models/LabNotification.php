<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'lab_id', 'type', 'title', 'message',
        'related_id', 'url', 'data', 'is_read', 'read_at',
    ];

    protected $casts = [
        'data'       => 'array',
        'is_read'    => 'boolean',
        'read_at'    => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeForLab($query, $labId)
    {
        return $query->where('lab_id', $labId);
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }

    public function getIconAttribute()
    {
        return $this->type === 'home_xray' ? 'fas fa-x-ray' : 'fas fa-flask';
    }

    public function getBadgeColorAttribute()
    {
        return $this->type === 'home_xray' ? 'info' : 'success';
    }

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public static function createNotification($labId, $type, $title, $message, $relatedId = null, $url = null, $data = [])
    {
        return self::create([
            'lab_id'     => $labId,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'related_id' => $relatedId,
            'url'        => $url,
            'data'       => $data,
            'is_read'    => false,
        ]);
    }
}
