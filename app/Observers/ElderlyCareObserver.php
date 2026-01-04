<?php

namespace App\Observers;

use App\Models\ElderlyCare;
use App\Services\AdminNotificationService;

class ElderlyCareObserver
{
    protected $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function created(ElderlyCare $appointment)
    {
        $this->notificationService->notifyNewAppointment($appointment, 'elderly_care');
    }
}