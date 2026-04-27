<?php

namespace App\Observers;

use App\Models\HomeXray;
use App\Services\AdminNotificationService;
use App\Services\LabNotificationService;

class HomeXrayObserver
{
    protected $adminNotificationService;
    protected $labNotificationService;

    public function __construct(AdminNotificationService $adminNotificationService, LabNotificationService $labNotificationService)
    {
        $this->adminNotificationService = $adminNotificationService;
        $this->labNotificationService   = $labNotificationService;
    }

    public function created(HomeXray $appointment)
    {
        $this->adminNotificationService->notifyNewAppointment($appointment, 'home_xray');
        $this->labNotificationService->notifyNewHomeXray($appointment);
    }
}