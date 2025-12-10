<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;
     protected $guarded = [];
    
    protected $casts = [
        'price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'number_of_cards' => 'integer',
    ];


     public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('assets/admin/uploads/' . $this->photo) : null;
    }
    
    /**
     * Get the POS that owns the card
     */
    public function pos()
    {
        return $this->belongsTo(POS::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    /**
     * Get the card numbers for the card
     */
    public function cardNumbers()
    {
        return $this->hasMany(CardNumber::class);
    }

    /**
     * Generate unique card numbers and create card_numbers records
     */
    public function generateCardNumbers()
    {
        // Delete existing card numbers for this card
        $this->cardNumbers()->delete();

        $generatedNumbers = [];
        $attempts = 0;
        $maxAttempts = $this->number_of_cards * 10; // Prevent infinite loop

        while (count($generatedNumbers) < $this->number_of_cards && $attempts < $maxAttempts) {
            $attempts++;
            
            // Generate a random 16-digit number (like credit card)
            $number = $this->generateUniqueNumber();
            
            // Check if this number already exists in database
            if (!CardNumber::where('number', $number)->exists() && !in_array($number, $generatedNumbers)) {
                $generatedNumbers[] = $number;
            }
        }

        // Create card_numbers records
        foreach ($generatedNumbers as $number) {
            $this->cardNumbers()->create([
                'number' => $number,
                'activate' => 1, // active
                'status' => 2,   // not used
            ]);
        }

        return $generatedNumbers;
    }

    /**
     * Generate a unique 16-digit number
     */
    private function generateUniqueNumber()
    {
        // Generate 4 groups of 4 digits
        $groups = [];
        for ($i = 0; $i < 4; $i++) {
            $groups[] = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        }
        
        return implode('', $groups);
    }

     public function cardUsages()
    {
        return $this->hasManyThrough(CardUsage::class, CardNumber::class);
    }

    // Accessor for available card numbers count
  public function getAvailableForSaleCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('sell', CardNumber::SELL_NOT_SOLD)
                   ->where('activate', CardNumber::ACTIVATE_ACTIVE)
                   ->where('status', CardNumber::STATUS_NOT_USED)
                   ->whereNull('assigned_user_id')
                   ->count();
    }

    // Accessor for sold but not assigned card numbers count
    public function getSoldNotAssignedCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('sell', CardNumber::SELL_SOLD)
                   ->whereNull('assigned_user_id')
                   ->count();
    }

    // Accessor for sold and assigned card numbers count
    public function getSoldAndAssignedCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('sell', CardNumber::SELL_SOLD)
                   ->whereNotNull('assigned_user_id')
                   ->where('status', CardNumber::STATUS_NOT_USED)
                   ->count();
    }

    // Accessor for available card numbers count (updated for backward compatibility)
    public function getAvailableCardNumbersCountAttribute()
    {
        return $this->available_for_sale_count;
    }

    // Accessor for assigned but not used card numbers count (updated)
    public function getAssignedNotUsedCardNumbersCountAttribute()
    {
        return $this->sold_and_assigned_count;
    }

    // Accessor for used card numbers count
    public function getUsedCardNumbersCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('status', CardNumber::STATUS_USED)
                   ->count();
    }

    // Accessor for inactive card numbers count
    public function getInactiveCardNumbersCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('activate', CardNumber::ACTIVATE_INACTIVE)
                   ->count();
    }

    // Accessor for active card numbers count (for backward compatibility)
    public function getActiveCardNumbersCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('activate', CardNumber::ACTIVATE_ACTIVE)
                   ->count();
    }

    // Accessor for unused card numbers count (for backward compatibility)
    public function getUnusedCardNumbersCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('status', CardNumber::STATUS_NOT_USED)
                   ->count();
    }
}
