<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportField extends Model
{
    use HasFactory;

     protected $guarded = [];

      protected $casts = [
        'required' => 'boolean'
    ];

    /**
     * Get the section this field belongs to
     */
    public function section()
    {
        return $this->belongsTo(ReportSection::class, 'report_section_id');
    }

    /**
     * Get field options for select/radio fields
     */
    public function options()
    {
        return $this->hasMany(ReportFieldOption::class);
    }

    /**
     * Get answers for this field
     */
    public function answers()
    {
        return $this->hasMany(ReportAnswer::class);
    }

    /**
     * Get localized label
     */
    public function getLabelAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->label_ar : $this->label_en;
    }

    /**
     * Get input type text
     */
    public function getInputTypeTextAttribute()
    {
        return __('messages.input_type_' . $this->input_type);
    }

    /**
     * Check if field needs options
     */
    public function needsOptions()
    {
        return in_array($this->input_type, ['select', 'radio', 'checkbox']);
    }
}
