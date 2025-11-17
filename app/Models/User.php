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
        return __('messages.' . $this->user_type);
    }
}
