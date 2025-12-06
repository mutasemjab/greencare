<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeElderlyCare extends Model
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
     * Get the care types available
     */
    public static function getCareTypes()
    {
        return [
            'elderly_care' => __('messages.elderly_care'),
            'patient_care' => __('messages.patient_care'),
            'mom' => __('messages.mom'),
            'child' => __('messages.child'),
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
     * Get the translated care type
     */
    public function getTranslatedCareTypeAttribute()
    {
        return __('messages.' . $this->type_of_care);
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' ' . __('messages.currency');
    }
}
