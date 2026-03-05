<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;



class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
    ];

    public function families()
    {
        return $this->belongsToMany(Family::class, 'family_users');
    }

    /**
     * Get user's family pivot records
     */
    public function familyUsers()
    {
        return $this->hasMany(FamilyUser::class);
    }

    /**
     * Get gender text
     */
    public function getGenderTextAttribute()
    {
        return $this->gender == 1 ? __('messages.male') : __('messages.female');
    }

    /**
     * Get user type text
     */
    public function getUserTypeTextAttribute()
    {
        $types = [
            'patient' => __('messages.patient'),
            'nurse' => __('messages.nurse'),
            'doctor' => __('messages.doctor'),
            'super_nurse' => __('messages.super_nurse'),
        ];

        return $types[$this->user_type] ?? $this->user_type;
    }

    /**
     * Get active status text
     */
    public function getActiveStatusTextAttribute()
    {
        return $this->activate == 1 ? __('messages.active') : __('messages.inactive');
    }

    /**
     * Get doctor type text (alias for user_type)
     */
    public function getDoctorTypeTextAttribute()
    {
        return $this->user_type_text;
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->activate == 1;
    }

    /**
     * Check if user is super nurse
     */
    public function isSuperNurse()
    {
        return $this->user_type === 'super_nurse';
    }

    /**
     * Scope for super nurses
     */
    public function scopeSuperNurses($query)
    {
        return $query->where('user_type', 'super_nurse');
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('activate', 1);
    }

    /**
     * Scope for inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('activate', 2);
    }
}
