<?php

namespace App\Observers;

use App\Models\HomeXray;
use App\Services\AdminNotificationService;

class HomeXrayObserver
{
    protected $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function created(HomeXray $appointment)
    {
        $this->notificationService->notifyNewAppointment($appointment, 'home_xray');
    }
}