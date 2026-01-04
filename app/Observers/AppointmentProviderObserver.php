<?php

namespace App\Observers;

use App\Models\AppointmentProvider;
use App\Services\AdminNotificationService;

class AppointmentProviderObserver
{
    protected $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function created(AppointmentProvider $appointment)
    {
        // Create notification for new appointment provider
        $this->notificationService->notifyNewAppointmentProvider($appointment);
    }
}