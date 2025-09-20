<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSection extends Model
{
    use HasFactory;
     protected $guarded = [];

      public function template()
    {
        return $this->belongsTo(ReportTemplate::class, 'report_template_id');
    }

    /**
     * Get all fields in this section
     */
    public function fields()
    {
        return $this->hasMany(ReportField::class)->orderBy('id');
    }

    /**
     * Get localized title
     */
    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }
}
