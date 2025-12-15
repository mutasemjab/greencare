<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferPatient extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date_of_transfer' => 'date',
        'time_of_transfer' => 'datetime',
        'from_lat' => 'double',
        'from_lng' => 'double',
        'to_lat' => 'double',
        'to_lng' => 'double',
        'from_place' => 'integer',
        'to_place' => 'integer',
    ];

    // Constants for place types
    const PLACE_INSIDE_AMMAN = 1;
    const PLACE_OUTSIDE_AMMAN = 2;

    /**
     * Get the user that owns the transfer
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted transfer datetime
     */
    public function getFormattedTransferAttribute()
    {
        $date = $this->date_of_transfer->format('Y-m-d');
        $time = $this->time_of_transfer ? $this->time_of_transfer->format('H:i') : '';
        return $time ? "$date $time" : $date;
    }

    /**
     * Get from place text
     */
    public function getFromPlaceTextAttribute()
    {
        return $this->from_place == self::PLACE_INSIDE_AMMAN 
            ? __('messages.inside_amman') 
            : __('messages.outside_amman');
    }

    /**
     * Get to place text
     */
    public function getToPlaceTextAttribute()
    {
        return $this->to_place == self::PLACE_INSIDE_AMMAN 
            ? __('messages.inside_amman') 
            : __('messages.outside_amman');
    }

    /**
     * Scope for today's transfers
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date_of_transfer', today());
    }

    /**
     * Scope for upcoming transfers
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date_of_transfer', '>=', today());
    }
}