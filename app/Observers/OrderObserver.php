<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\AdminNotificationService;

class OrderObserver
{
    protected $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function created(Order $order)
    {
        // Create notification for new order
        $this->notificationService->notifyNewOrder($order);
    }
}