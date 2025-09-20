<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportFieldOption extends Model
{
    use HasFactory;
     protected $guarded = [];

      public function field()
    {
        return $this->belongsTo(ReportField::class, 'report_field_id');
    }

    /**
     * Get localized value
     */
    public function getValueAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->value_ar : $this->value_en;
    }
}
