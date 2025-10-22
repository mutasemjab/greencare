<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeRequestNurse extends Model
{
    use HasFactory;
     protected $guarded = [];
     protected $casts = [
        'price' => 'double',
    ];

    /**
     * Get the service types available
     */
    public static function getServiceTypes()
    {
        return [
            'hour' => __('messages.hour'),
            'day' => __('messages.day'),
            'sleep' => __('messages.sleep'),
            'number_of_days' => __('messages.number_of_days'),
        ];
    }

    /**
     * Get the translated service type
     */
    public function getTranslatedServiceTypeAttribute()
    {
        return __('messages.' . $this->type_of_service);
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' ' . __('messages.currency');
    }
}
