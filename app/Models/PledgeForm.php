<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PledgeForm extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'date_of_pledge' => 'date',
        'date_of_birth' => 'date',
    ];

    /**
     * Get the room that belongs to the pledge form.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Check if this is an authorization form
     */
    public function isAuthorizationForm()
    {
        return $this->type === 'authorization_form';
    }

    /**
     * Check if this is a pledge form
     */
    public function isPledgeForm()
    {
        return $this->type === 'pledge_form';
    }
}
