<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentResult extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'files' => 'array',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the parent appointment model (MedicalTest or HomeXray).
     */
    public function appointment()
    {
        return $this->morphTo();
    }

    /**
     * Get the lab that uploaded the results.
     */
    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }

    /**
     * Get all file URLs
     */
    public function getFileUrlsAttribute()
    {
        if (!$this->files) {
            return [];
        }

        return array_map(function ($file) {
            return url('assets/admin/uploads/' . $file);
        }, $this->files);
    }
}