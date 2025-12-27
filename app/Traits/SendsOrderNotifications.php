<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Notification;
use App\Http\Controllers\Admin\FCMController;

trait SendsOrderNotifications
{
    /**
     * Send order created notification to user
     */
    protected function sendOrderCreatedNotification($order)
    {
        try {
            $user = $order->user;
            
            if (!$user) {
                return;
            }

            $title = '🛍️ تم إنشاء طلبك بنجاح';
            $body = "تم إنشاء الطلب رقم #{$order->number} بنجاح. إجمالي المبلغ: {$order->total_prices} دينار";
            
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
                    'order_created'
                );
            }
            
            \Log::info("Order created notification sent for order #{$order->number}");
            
        } catch (\Exception $e) {
            \Log::error('Error sending order created notification: ' . $e->getMessage());
        }
    }

    /**
     * Send order status change notification
     */
    protected function sendOrderStatusNotification($order, $oldStatus, $newStatus)
    {
        try {
            $user = $order->user;
            
            if (!$user) {
                return;
            }

            $statusMessages = [
                1 => ['title' => '⏳ طلبك قيد المعالجة', 'body' => 'طلبك رقم #%s قيد المعالجة'],
                2 => ['title' => '✅ تم قبول طلبك', 'body' => 'تم قبول طلبك رقم #%s وجاري التحضير'],
                3 => ['title' => '🚚 طلبك في الطريق', 'body' => 'طلبك رقم #%s في طريقه إليك'],
                4 => ['title' => '🎉 تم توصيل طلبك', 'body' => 'تم توصيل طلبك رقم #%s بنجاح. شكراً لثقتك بنا!'],
                5 => ['title' => '❌ تم إلغاء الطلب', 'body' => 'تم إلغاء طلبك رقم #%s'],
                6 => ['title' => '💰 تم استرجاع المبلغ', 'body' => 'تم استرجاع مبلغ طلبك رقم #%s بنجاح'],
            ];

            $statusData = $statusMessages[$newStatus] ?? null;
            
            if (!$statusData) {
                return;
            }

            $title = $statusData['title'];
            $body = sprintf($statusData['body'], $order->number);
            
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
                    'order_status_changed'
                );
            }
            
            \Log::info("Order status notification sent for order #{$order->number} - Status: {$newStatus}");
            
        } catch (\Exception $e) {
            \Log::error('Error sending order status notification: ' . $e->getMessage());
        }
    }

    /**
     * Send payment status notification
     */
    protected function sendPaymentStatusNotification($order, $newPaymentStatus)
    {
        try {
            $user = $order->user;
            
            if (!$user || $newPaymentStatus != 1) { // Only notify when payment is confirmed
                return;
            }

            $title = '✅ تم تأكيد الدفع';
            $body = "تم تأكيد دفع طلبك رقم #{$order->number} بنجاح";
            
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
                    'payment_confirmed'
                );
            }
            
            \Log::info("Payment confirmed notification sent for order #{$order->number}");
            
        } catch (\Exception $e) {
            \Log::error('Error sending payment status notification: ' . $e->getMessage());
        }
    }
}