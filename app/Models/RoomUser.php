<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomUser extends Model
{
    use HasFactory;
     protected $guarded = [];

     public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get role text
     */
    public function getRoleTextAttribute()
    {
        return __('messages.' . $this->role);
    }

    /**
     * Get role badge class
     */
    public function getRoleBadgeClassAttribute()
    {
        $classes = [
            'patient' => 'badge-warning',
            'family' => 'badge-info',
            'doctor' => 'badge-primary',
            'nurse' => 'badge-success'
        ];

        return $classes[$this->role] ?? 'badge-secondary';
    }
}
