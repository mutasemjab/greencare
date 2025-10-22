<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppointmentProvider extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date_of_appointment' => 'date',
        'time_of_appointment' => 'datetime:H:i',
        'lat' => 'double',
        'lng' => 'double',
        'status' => 'integer',
    ];

    /**
     * Get the provider that belongs to the appointment.
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the user that owns the appointment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted appointment datetime
     */
    public function getFormattedAppointmentAttribute()
    {
        $date = $this->date_of_appointment ? $this->date_of_appointment->format('Y-m-d') : __('messages.not_specified');
        $time = $this->time_of_appointment ? $this->time_of_appointment->format('H:i') : __('messages.not_specified');
        return $date . ' ' . $time;
    }

     // Status constants
    const STATUS_PENDING = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_ON_THE_WAY = 3;
    const STATUS_DELIVERED = 4;
    const STATUS_CANCELED = 5;

    /**
     * Get status options
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_ON_THE_WAY => 'On The Way',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELED => 'Canceled',
        ];
    }

    /**
     * Get status name
     */
    public function getStatusNameAttribute()
    {
        $statuses = self::getStatusOptions();
        return $statuses[$this->status] ?? 'Unknown';
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'bg-warning';
            case self::STATUS_ACCEPTED:
                return 'bg-info';
            case self::STATUS_ON_THE_WAY:
                return 'bg-primary';
            case self::STATUS_DELIVERED:
                return 'bg-success';
            case self::STATUS_CANCELED:
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if appointment is pending
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if appointment is accepted
     */
    public function isAccepted()
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    /**
     * Check if appointment is on the way
     */
    public function isOnTheWay()
    {
        return $this->status === self::STATUS_ON_THE_WAY;
    }

    /**
     * Check if appointment is delivered
     */
    public function isDelivered()
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    /**
     * Check if appointment is canceled
     */
    public function isCanceled()
    {
        return $this->status === self::STATUS_CANCELED;
    }

}
