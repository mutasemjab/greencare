<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestNurse extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected $casts = [
        'date_of_appointment' => 'date',
        'time_of_appointment' => 'datetime:H:i',
    ];

    /**
     * Get the user that owns the appointment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the elderly care type.
     */
    public function typeRequestNurse()
    {
        return $this->belongsTo(TypeRequestNurse::class);
    }

    /**
     * Get formatted appointment datetime
     */
    public function getFormattedAppointmentAttribute()
    {
        $date = $this->date_of_appointment->format('Y-m-d');
        $time = $this->time_of_appointment ? $this->time_of_appointment->format('H:i') : __('messages.not_specified');
        return $date . ' ' . $time;
    }
}
