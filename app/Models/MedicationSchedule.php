<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationSchedule extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'time' => 'datetime:H:i:s'
    ];

    /**
     * Get the time in H:i format for input value
     */
    public function getTimeForInputAttribute()
    {
        return $this->time ? $this->time->format('H:i') : '';
    }

    /**
     * Get the medication this schedule belongs to
     */
    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * Get frequency text
     */
    public function getFrequencyTextAttribute()
    {
        return __('messages.frequency_' . $this->frequency);
    }

    /**
     * Get formatted time
     */
    public function getFormattedTimeAttribute()
    {
        return $this->time;
    }
}
