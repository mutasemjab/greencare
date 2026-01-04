<?php

namespace App\Services;

use App\Models\AdminNotification;

class AdminNotificationService
{
    /**
     * Create new order notification
     */
    public function notifyNewOrder($order)
    {
        return AdminNotification::createNotification(
            type: 'order',
            title: __('messages.new_order'),
            message: __('messages.new_order_notification_message', [
                'number' => $order->number,
                'customer' => $order->user->name ?? __('messages.guest'),
                'amount' => number_format($order->total_prices, 2)
            ]),
            relatedId: $order->id,
            url: route('orders.show', $order->id),
            data: [
                'order_number' => $order->number,
                'customer_name' => $order->user->name ?? null,
                'total_amount' => $order->total_prices
            ]
        );
    }

    /**
     * Create new appointment notification
     */
    public function notifyNewAppointment($appointment, $type)
    {
        $typeLabel = __('messages.' . $type);
        
        return AdminNotification::createNotification(
            type: 'appointment',
            title: __('messages.new_appointment'),
            message: __('messages.new_appointment_notification_message', [
                'type' => $typeLabel,
                'user' => $appointment->user->name ?? __('messages.unknown'),
                'date' => $appointment->date_of_appointment ?? __('messages.not_specified')
            ]),
            relatedId: $appointment->id,
            url: route('appointments.index', ['type' => $type]),
            data: [
                'appointment_type' => $type,
                'user_name' => $appointment->user->name ?? null,
                'appointment_date' => $appointment->date_of_appointment
            ]
        );
    }

    /**
     * Create new appointment provider notification
     */
    public function notifyNewAppointmentProvider($appointment)
    {
        return AdminNotification::createNotification(
            type: 'appointment_provider',
            title: __('messages.new_provider_appointment'),
            message: __('messages.new_provider_appointment_notification_message', [
                'patient' => $appointment->name_of_patient,
                'phone' => $appointment->phone_of_patient,
                'provider' => $appointment->provider->name ?? __('messages.not_assigned')
            ]),
            relatedId: $appointment->id,
            url: route('appointment-providers.show', $appointment->id),
            data: [
                'patient_name' => $appointment->name_of_patient,
                'patient_phone' => $appointment->phone_of_patient,
                'provider_name' => $appointment->provider->name ?? null
            ]
        );
    }

    /**
     * Get unread count
     */
    public function getUnreadCount()
    {
        return AdminNotification::unread()->count();
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        return AdminNotification::unread()->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Delete old read notifications (older than 30 days)
     */
    public function cleanupOldNotifications($days = 30)
    {
        return AdminNotification::read()
            ->where('read_at', '<', now()->subDays($days))
            ->delete();
    }
}