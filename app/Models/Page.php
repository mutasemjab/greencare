<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    const TYPE_TERMS_AND_CONDITIONS = 1;
    const TYPE_PRIVACY_POLICY = 2;

    /**
     * Get page type name
     */
    public function getTypeNameAttribute()
    {
        return match($this->type) {
            self::TYPE_TERMS_AND_CONDITIONS => __('messages.terms_and_conditions'),
            self::TYPE_PRIVACY_POLICY => __('messages.privacy_policy'),
            default => __('messages.unknown_type'),
        };
    }

    /**
     * Get localized title
     */
    public function getLocalizedTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    /**
     * Get localized description
     */
    public function getLocalizedDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    /**
     * Scope for terms and conditions
     */
    public function scopeTermsAndConditions($query)
    {
        return $query->where('type', self::TYPE_TERMS_AND_CONDITIONS);
    }

    /**
     * Scope for privacy policy
     */
    public function scopePrivacyPolicy($query)
    {
        return $query->where('type', self::TYPE_PRIVACY_POLICY);
    }
}
