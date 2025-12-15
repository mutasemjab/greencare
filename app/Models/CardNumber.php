<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CardNumber extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    // Your existing constants
    const ACTIVATE_ACTIVE = 1;
    const ACTIVATE_INACTIVE = 2;
    
    const STATUS_USED = 1;
    const STATUS_NOT_USED = 2;
    
    const SELL_SOLD = 1;
    const SELL_NOT_SOLD = 2;

    // Relationships
    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function usages()
    {
        return $this->hasMany(CardUsage::class);
    }

    // Accessors for usage tracking
    public function getUsageCountAttribute()
    {
        return $this->usages()->count();
    }

    public function getRemainingUsesAttribute()
    {
        $totalAllowed = $this->card->number_of_use_for_one_card ?? 0;
        $used = $this->usage_count;
        return max(0, $totalAllowed - $used);
    }

    public function hasRemainingUses()
    {
        return $this->remaining_uses > 0;
    }

    public function getUsagePercentageAttribute()
    {
        $totalAllowed = $this->card->number_of_use_for_one_card ?? 1;
        return round(($this->usage_count / $totalAllowed) * 100, 1);
    }

    // Your existing helper methods
    public function isAvailableForSale()
    {
        return $this->sell == self::SELL_NOT_SOLD 
            && $this->activate == self::ACTIVATE_ACTIVE 
            && $this->status == self::STATUS_NOT_USED 
            && is_null($this->assigned_user_id);
    }

    public function isSoldNotAssigned()
    {
        return $this->sell == self::SELL_SOLD 
            && is_null($this->assigned_user_id);
    }

    public function isSoldAndAssigned()
    {
        return $this->sell == self::SELL_SOLD 
            && !is_null($this->assigned_user_id) 
            && $this->status == self::STATUS_NOT_USED;
    }

    public function isUsed()
    {
        return $this->status == self::STATUS_USED;
    }

    public function getStatusBadgeClass()
    {
        if ($this->isAvailableForSale()) {
            return 'bg-success';
        } elseif ($this->isSoldNotAssigned()) {
            return 'bg-info';
        } elseif ($this->isSoldAndAssigned()) {
            return 'bg-warning';
        } elseif ($this->isUsed()) {
            return 'bg-danger';
        }
        return 'bg-secondary';
    }

    public function getStatusText()
    {
        if ($this->isAvailableForSale()) {
            return __('messages.available_for_sale');
        } elseif ($this->isSoldNotAssigned()) {
            return __('messages.sold_not_assigned');
        } elseif ($this->isSoldAndAssigned()) {
            return __('messages.sold_assigned');
        } elseif ($this->isUsed()) {
            return __('messages.used');
        }
        return __('messages.unknown_status');
    }
}
