<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomReportTemplateHistory extends Model
{
    
    protected $guarded = [];
    
    protected $casts = [
        'assigned_at' => 'datetime',
        'replaced_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function template()
    {
        return $this->belongsTo(ReportTemplate::class, 'report_template_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get reports created during this template's active period
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'report_template_id', 'report_template_id')
            ->where('room_id', $this->room_id)
            ->when($this->replaced_at, function($query) {
                $query->whereBetween('created_at', [$this->assigned_at, $this->replaced_at]);
            }, function($query) {
                $query->where('created_at', '>=', $this->assigned_at);
            });
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for historical templates
     */
    public function scopeHistorical($query)
    {
        return $query->where('is_active', false);
    }
}