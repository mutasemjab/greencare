<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    protected $casts = [
        'number_of_use_for_one_card' => 'integer',
        'selling_price' => 'decimal:2',
        'number_of_cards' => 'integer',
    ];

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('assets/admin/uploads/' . $this->photo) : null;
    }
    
    public function pos()
    {
        return $this->belongsTo(POS::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function cardNumbers()
    {
        return $this->hasMany(CardNumber::class);
    }

    public function generateCardNumbers()
    {
        $this->cardNumbers()->delete();

        $generatedNumbers = [];
        $attempts = 0;
        $maxAttempts = $this->number_of_cards * 10;

        while (count($generatedNumbers) < $this->number_of_cards && $attempts < $maxAttempts) {
            $attempts++;
            
            $number = $this->generateUniqueNumber();
            
            if (!CardNumber::where('number', $number)->exists() && !in_array($number, $generatedNumbers)) {
                $generatedNumbers[] = $number;
            }
        }

        foreach ($generatedNumbers as $number) {
            $this->cardNumbers()->create([
                'number' => $number,
                'activate' => 1,
                'status' => 2,
            ]);
        }

        return $generatedNumbers;
    }

    private function generateUniqueNumber()
    {
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

    public function getAvailableForSaleCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('sell', CardNumber::SELL_NOT_SOLD)
                   ->where('activate', CardNumber::ACTIVATE_ACTIVE)
                   ->where('status', CardNumber::STATUS_NOT_USED)
                   ->whereNull('assigned_user_id')
                   ->count();
    }

    public function getSoldNotAssignedCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('sell', CardNumber::SELL_SOLD)
                   ->whereNull('assigned_user_id')
                   ->count();
    }

    public function getSoldAndAssignedCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('sell', CardNumber::SELL_SOLD)
                   ->whereNotNull('assigned_user_id')
                   ->where('status', CardNumber::STATUS_NOT_USED)
                   ->count();
    }

    public function getAvailableCardNumbersCountAttribute()
    {
        return $this->available_for_sale_count;
    }

    public function getAssignedNotUsedCardNumbersCountAttribute()
    {
        return $this->sold_and_assigned_count;
    }

    public function getUsedCardNumbersCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('status', CardNumber::STATUS_USED)
                   ->count();
    }

    public function getInactiveCardNumbersCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('activate', CardNumber::ACTIVATE_INACTIVE)
                   ->count();
    }

    public function getActiveCardNumbersCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('activate', CardNumber::ACTIVATE_ACTIVE)
                   ->count();
    }

    public function getUnusedCardNumbersCountAttribute()
    {
        return $this->cardNumbers()
                   ->where('status', CardNumber::STATUS_NOT_USED)
                   ->count();
    }
}
