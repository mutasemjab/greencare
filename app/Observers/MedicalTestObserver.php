<?php

namespace App\Observers;

use App\Models\MedicalTest;
use App\Services\AdminNotificationService;
use App\Services\LabNotificationService;

class MedicalTestObserver
{
    protected $adminNotificationService;
    protected $labNotificationService;

    public function __construct(AdminNotificationService $adminNotificationService, LabNotificationService $labNotificationService)
    {
        $this->adminNotificationService = $adminNotificationService;
        $this->labNotificationService   = $labNotificationService;
    }

    public function created(MedicalTest $appointment)
    {
        $this->adminNotificationService->notifyNewAppointment($appointment, 'medical_test');
        $this->labNotificationService->notifyNewMedicalTest($appointment);
    }
}