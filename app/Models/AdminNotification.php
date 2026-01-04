<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;
      protected $fillable = [
        'type',
        'title',
        'message',
        'related_id',
        'url',
        'data',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    /**
     * Get icon based on type
     */
    public function getIconAttribute()
    {
        $icons = [
            'order' => 'fas fa-shopping-cart',
            'appointment' => 'fas fa-calendar-check',
            'appointment_provider' => 'fas fa-user-md',
        ];

        return $icons[$this->type] ?? 'fas fa-bell';
    }

    /**
     * Get badge color based on type
     */
    public function getBadgeColorAttribute()
    {
        $colors = [
            'order' => 'primary',
            'appointment' => 'success',
            'appointment_provider' => 'info',
        ];

        return $colors[$this->type] ?? 'secondary';
    }

    /**
     * Get human readable time
     */
    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Static method to create notification
     */
    public static function createNotification($type, $title, $message, $relatedId = null, $url = null, $data = [])
    {
        return self::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_id' => $relatedId,
            'url' => $url,
            'data' => $data,
            'is_read' => false
        ]);
    }
}
