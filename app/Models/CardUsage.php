<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardUsage extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'used_at' => 'datetime',
    ];

    /**
     * Get the driver that used the card
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the card number that was used
     */
    public function cardNumber()
    {
        return $this->belongsTo(CardNumber::class);
    }

    /**
     * Get the card through card number
     */
    public function card()
    {
        return $this->hasOneThrough(Card::class, CardNumber::class, 'id', 'id', 'card_number_id', 'card_id');
    }
}
