<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Room;
use App\Models\Notification;
use App\Http\Controllers\Admin\FCMController;

trait SendsAppointmentNotifications
{
    /**
     * Send appointment created notification to user
     */
    protected function sendAppointmentCreatedNotification($appointment, $serviceType, $serviceName, $finalPrice)
    {
        try {
            $user = $appointment->user;
            
            if (!$user) {
                return;
            }

            $serviceTypeArabic = $this->getServiceTypeInArabic($serviceType);
            
            $title = '📅 تم حجز موعدك بنجاح';
            $body = "تم حجز موعد {$serviceTypeArabic} ({$serviceName}) بتاريخ {$appointment->date_of_appointment->format('Y-m-d')}. السعر النهائي: {$finalPrice} دينار";
            
            // Save notification in database
            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
            ]);
            
            // Send FCM notification if user has token
            if ($user->fcm_token) {
                FCMController::sendMessage(
                    $title,
                    $body,
                    $user->fcm_token,
                    $user->id,
                    'appointment_created'
                );
            }
            
            \Log::info("Appointment created notification sent for user #{$user->id}");
            
        } catch (\Exception $e) {
            \Log::error('Error sending appointment created notification: ' . $e->getMessage());
        }
    }

    /**
     * Send appointment notification to room members
     */
    protected function sendAppointmentToRoomNotification($appointment, $room, $serviceType, $serviceName, $finalPrice)
    {
        try {
            if (!$room) {
                return;
            }

            $roomUsers = $room->users()
                ->where('users.id', '!=', $appointment->user_id)
                ->get();

            if ($roomUsers->isEmpty()) {
                return;
            }

            $serviceTypeArabic = $this->getServiceTypeInArabic($serviceType);
            $userName = $appointment->user->name;
            
            $title = '📅 موعد جديد في الغرفة';
            $body = "تم حجز موعد {$serviceTypeArabic} ({$serviceName}) من قبل {$userName} في الغرفة: {$room->title}";

            foreach ($roomUsers as $user) {
                // Save notification in database
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'body' => $body,
                ]);

                // Send FCM notification if user has token
                if ($user->fcm_token) {
                    FCMController::sendMessage(
                        $title,
                        $body,
                        $user->fcm_token,
                        $user->id,
                        'room_appointment_created'
                    );
                }
            }

            \Log::info("Room appointment notifications sent for room #{$room->id}");

        } catch (\Exception $e) {
            \Log::error('Error sending room appointment notification: ' . $e->getMessage());
        }
    }

    /**
     * Send shower appointment created notification
     */
    protected function sendShowerAppointmentNotification($shower, $finalPrice, $discountInfo = null)
    {
        try {
            $user = $shower->user;
            
            if (!$user) {
                return;
            }

            $title = '🚿 تم حجز موعد الاستحمام';
            $body = "تم حجز موعد الاستحمام بتاريخ {$shower->date_of_shower->format('Y-m-d')}";
            
            if ($shower->time_of_shower) {
                $body .= " الساعة {$shower->time_of_shower->format('H:i')}";
            }
            
            $body .= ". السعر: {$finalPrice} دينار";
            
            if ($discountInfo && isset($discountInfo['discount_percentage'])) {
                $body .= " (بعد خصم {$discountInfo['discount_percentage']}%)";
            }
            
            // Save notification in database
            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
            ]);
            
            // Send FCM notification if user has token
            if ($user->fcm_token) {
                FCMController::sendMessage(
                    $title,
                    $body,
                    $user->fcm_token,
                    $user->id,
                    'shower_appointment_created'
                );
            }
            
            \Log::info("Shower appointment notification sent for user #{$user->id}");
            
        } catch (\Exception $e) {
            \Log::error('Error sending shower appointment notification: ' . $e->getMessage());
        }
    }

    /**
     * Send shower appointment to room members
     */
    protected function sendShowerToRoomNotification($shower, $room, $finalPrice)
    {
        try {
            if (!$room) {
                return;
            }

            $roomUsers = $room->users()
                ->where('users.id', '!=', $shower->user_id)
                ->get();

            if ($roomUsers->isEmpty()) {
                return;
            }

            $userName = $shower->user->name;
            
            $title = '🚿 موعد استحمام جديد في الغرفة';
            $body = "تم حجز موعد استحمام من قبل {$userName} في الغرفة بتاريخ {$shower->date_of_shower->format('Y-m-d')}";

            foreach ($roomUsers as $user) {
                // Save notification in database
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'body' => $body,
                ]);

                // Send FCM notification if user has token
                if ($user->fcm_token) {
                    FCMController::sendMessage(
                        $title,
                        $body,
                        $user->fcm_token,
                        $user->id,
                        'room_shower_appointment'
                    );
                }
            }

            \Log::info("Room shower notifications sent for room #{$room->id}");

        } catch (\Exception $e) {
            \Log::error('Error sending room shower notification: ' . $e->getMessage());
        }
    }

    /**
     * Get service type in Arabic
     */
    private function getServiceTypeInArabic($serviceType)
    {
        $types = [
            'elderly_care' => 'رعاية المسنين',
            'request_nurse' => 'طلب ممرض',
            'home_xray' => 'أشعة منزلية',
            'medical_test' => 'فحص طبي',
        ];

        return $types[$serviceType] ?? $serviceType;
    }
}