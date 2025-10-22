<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shower extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected $casts = [
        'date_of_shower' => 'date',
        'time_of_shower' => 'datetime:H:i',
        'price' => 'double',
    ];

    /**
     * Get the user that owns the shower appointment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted shower datetime
     */
    public function getFormattedShowerAttribute()
    {
        $date = $this->date_of_shower ? $this->date_of_shower->format('Y-m-d') : __('messages.not_specified');
        $time = $this->time_of_shower ? $this->time_of_shower->format('H:i') : __('messages.not_specified');
        return $date . ' ' . $time;
    }


    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date_of_shower', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
