<?php

namespace App\Observers;

use App\Models\RequestNurse;
use App\Services\AdminNotificationService;

class RequestNurseObserver
{
    protected $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function created(RequestNurse $appointment)
    {
        $this->notificationService->notifyNewAppointment($appointment, 'request_nurse');
    }
}