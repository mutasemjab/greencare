<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * Get the patient this medication belongs to
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the room this medication belongs to
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

     public function diagnosis()
    {
        return $this->belongsTo(PatientDiagnose::class, 'diagnosis_id');
    }


    /**
     * Get medication schedules
     */
    public function schedules()
    {
        return $this->hasMany(MedicationSchedule::class);
    }

    /**
     * Get medication logs
     */
    public function logs()
    {
        return $this->hasMany(MedicationLog::class);
    }

    /**
     * Get recent logs
     */
    public function recentLogs()
    {
        return $this->logs()->orderBy('scheduled_time', 'desc')->limit(10);
    }

    /**
     * Get compliance rate (percentage of taken medications)
     */
    public function getComplianceRateAttribute()
    {
        $totalLogs = $this->logs()->count();
        if ($totalLogs === 0) return 0;
        
        $takenLogs = $this->logs()->where('taken', true)->count();
        return round(($takenLogs / $totalLogs) * 100, 1);
    }

    /**
     * Get next scheduled time
     */
    public function getNextScheduledTimeAttribute()
    {
        return $this->logs()
                   ->where('scheduled_time', '>', now())
                   ->where('taken', false)
                   ->orderBy('scheduled_time')
                   ->first()?->scheduled_time;
    }
}
