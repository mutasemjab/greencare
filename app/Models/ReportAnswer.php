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
     * Get formatted value for display.
     *
     * Values are stored via json_encode() in the controller AND the model has a
     * 'json' cast, causing double-encoding. This accessor peels both layers so the
     * view always receives a clean PHP scalar or array.
     */
    public function getFormattedValueAttribute()
    {
        $val = $this->value; // JSON cast already decoded once

        if (is_null($val)) {
            return '-';
        }

        // If the cast returned a string that is itself valid JSON (i.e. the value
        // was double-encoded), decode one more time to reach the real value.
        if (is_string($val)) {
            $decoded = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && $decoded !== $val) {
                $val = $decoded;
            }
        }

        if (is_array($val)) {
            return implode(', ', $val);
        }

        return (string) $val;
    }
}
