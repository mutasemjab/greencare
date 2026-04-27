<?php

namespace App\Services;

use App\Models\LabNotification;

class LabNotificationService
{
    public function notifyNewMedicalTest($appointment)
    {
        if (!$appointment->lab_id) {
            return null;
        }

        return LabNotification::createNotification(
            labId:     $appointment->lab_id,
            type:      'medical_test',
            title:     'طلب تحليل جديد',
            message:   'طلب تحليل جديد من ' . ($appointment->user->name ?? 'مجهول') . ' بتاريخ ' . $appointment->date_of_appointment,
            relatedId: $appointment->id,
            url:       route('lab.appointments.show', ['type' => 'medical_test', 'id' => $appointment->id]),
            data: [
                'user_name'        => $appointment->user->name ?? null,
                'appointment_date' => $appointment->date_of_appointment,
            ]
        );
    }

    public function notifyNewHomeXray($appointment)
    {
        if (!$appointment->lab_id) {
            return null;
        }

        return LabNotification::createNotification(
            labId:     $appointment->lab_id,
            type:      'home_xray',
            title:     'طلب أشعة منزلية جديد',
            message:   'طلب أشعة منزلية من ' . ($appointment->user->name ?? 'مجهول') . ' بتاريخ ' . $appointment->date_of_appointment,
            relatedId: $appointment->id,
            url:       route('lab.appointments.show', ['type' => 'home_xray', 'id' => $appointment->id]),
            data: [
                'user_name'        => $appointment->user->name ?? null,
                'appointment_date' => $appointment->date_of_appointment,
            ]
        );
    }

    public function getUnreadCount($labId)
    {
        return LabNotification::forLab($labId)->unread()->count();
    }

    public function markAllAsRead($labId)
    {
        return LabNotification::forLab($labId)->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function cleanupOldNotifications($labId, $days = 30)
    {
        return LabNotification::forLab($labId)->read()
            ->where('read_at', '<', now()->subDays($days))
            ->delete();
    }
}
