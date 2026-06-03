<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationSchedule extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'time'          => 'datetime:H:i:s',
        'day_of_week'   => 'integer',
        'day_of_month'  => 'integer',
    ];

    protected $appends = ['day_of_week_label'];

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
        return $this->time ? $this->time->format('H:i') : '';
    }

    public function getDayOfWeekLabelAttribute()
    {
        $days = [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];
        return $this->day_of_week !== null ? ($days[$this->day_of_week] ?? null) : null;
    }
}
