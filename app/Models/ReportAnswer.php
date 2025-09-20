<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportAnswer extends Model
{
    use HasFactory;
     protected $guarded = [];

       protected $casts = [
        'value' => 'json'
    ];

    /**
     * Get the report this answer belongs to
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the field this answer is for
     */
    public function field()
    {
        return $this->belongsTo(ReportField::class, 'report_field_id');
    }

    /**
     * Get formatted value for display
     */
    public function getFormattedValueAttribute()
    {
        if (is_null($this->value)) {
            return '-';
        }

        if (is_array($this->value)) {
            return implode(', ', $this->value);
        }

        return $this->value;
    }
}
