<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $guarded = [];

     public function family()
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Get all room users
     */
    public function roomUsers()
    {
        return $this->hasMany(RoomUser::class);
    }

    /**
     * Get all users in this room
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'room_users')->withPivot('role')->withTimestamps();
    }

    /**
     * Get patients in this room
     */
    public function patients()
    {
        return $this->belongsToMany(User::class, 'room_users')
                   ->wherePivot('role', 'patient')
                   ->withPivot('role')
                   ->withTimestamps();
    }

    /**
     * Get doctors in this room
     */
    public function doctors()
    {
        return $this->belongsToMany(User::class, 'room_users')
                   ->wherePivot('role', 'doctor')
                   ->withPivot('role')
                   ->withTimestamps();
    }

    /**
     * Get nurses in this room
     */
    public function nurses()
    {
        return $this->belongsToMany(User::class, 'room_users')
                   ->wherePivot('role', 'nurse')
                   ->withPivot('role')
                   ->withTimestamps();
    }

    /**
     * Get family members in this room
     */
    public function familyMembers()
    {
        return $this->belongsToMany(User::class, 'room_users')
                   ->wherePivot('role', 'family')
                   ->withPivot('role')
                   ->withTimestamps();
    }

    /**
     * Get reports for this room
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get medications for this room
     */
    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

    /**
     * Get active medications
     */
    public function activeMedications()
    {
        return $this->medications()->where('active', true);
    }

    /**
     * Get room statistics
     */
    public function getStatsAttribute()
    {
        return [
            'patients_count' => $this->patients()->count(),
            'doctors_count' => $this->doctors()->count(),
            'nurses_count' => $this->nurses()->count(),
            'family_count' => $this->familyMembers()->count(),
            'reports_count' => $this->reports()->count(),
            'medications_count' => $this->medications()->count(),
        ];
    }
}
