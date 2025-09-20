<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationLog extends Model
{
    use HasFactory;
    protected $guarded = [];

     protected $casts = [
        'scheduled_time' => 'datetime',
        'taken_at' => 'datetime',
        'taken' => 'boolean'
    ];

    /**
     * Get the medication this log belongs to
     */
    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        if ($this->taken) {
            return __('messages.taken');
        }
        
        if ($this->scheduled_time < now()) {
            return __('messages.missed');
        }
        
        return __('messages.pending');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        if ($this->taken) {
            return 'badge-success';
        }
        
        if ($this->scheduled_time < now()) {
            return 'badge-danger';
        }
        
        return 'badge-warning';
    }

    /**
     * Check if this log is overdue
     */
    public function getIsOverdueAttribute()
    {
        return !$this->taken && $this->scheduled_time < now();
    }
}
