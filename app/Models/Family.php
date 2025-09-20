<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;
    protected $guarded = [];
     /**
     * Get all users belonging to this family
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'family_users');
    }

    /**
     * Get family users pivot records
     */
    public function familyUsers()
    {
        return $this->hasMany(FamilyUser::class);
    }
}
