<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalTest extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $casts = [
        'date_of_appointment' => 'date',
        'time_of_appointment' => 'datetime:H:i',
    ];

    protected $appends = ['status_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function typeMedicalTest()
    {
        return $this->belongsTo(TypeMedicalTest::class);
    }

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }

    public function result()
    {
        return $this->morphOne(AppointmentResult::class, 'appointment');
    }

    public function getFormattedAppointmentAttribute()
    {
        $date = $this->date_of_appointment->format('Y-m-d');
        $time = $this->time_of_appointment ? $this->time_of_appointment->format('H:i') : __('messages.not_specified');
        return $date . ' ' . $time;
    }

    public function getStatusNameAttribute()
    {
        return __('messages.status_' . $this->status);
    }
}