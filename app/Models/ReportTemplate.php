<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportTemplate extends Model
{
    use HasFactory;
     protected $guarded = [];
    public function sections()
    {
        return $this->hasMany(ReportSection::class)->orderBy('order');
    }

    /**
     * Get reports using this template
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get localized title
     */
    public function getTitleAttribute()
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    /**
     * Get created for text
     */
    public function getCreatedForTextAttribute()
    {
        return __('messages.' . $this->created_for);
    }

    /**
     * Scope for doctor templates
     */
    public function scopeForDoctors($query)
    {
        return $query->where('created_for', 'doctor');
    }

    /**
     * Scope for nurse templates
     */
    public function scopeForNurses($query)
    {
        return $query->where('created_for', 'nurse');
    }
}
