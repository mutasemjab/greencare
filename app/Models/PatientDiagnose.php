<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientDiagnose extends Model
{
    protected $guarded = [];

    public function appointment()
    {
        return $this->belongsTo(AppointmentProvider::class, 'appointment_provider_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function diagnosedBy()
    {
        return $this->belongsTo(User::class, 'diagnosed_by');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class, 'diagnosis_id');
    }
}