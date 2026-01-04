<?php

namespace App\Observers;

use App\Models\MedicalTest;
use App\Services\AdminNotificationService;

class MedicalTestObserver
{
    protected $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function created(MedicalTest $appointment)
    {
        $this->notificationService->notifyNewAppointment($appointment, 'medical_test');
    }
}