<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FirestoreMessageService
{
    protected $projectId;
    protected $baseUrl;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    }

    /**
     * Send order details message to room
     * 
     * @param int $roomId
     * @param array $orderData
     * @param int $senderId
     * @param string $senderName
     * @return bool
     */
    public function sendOrderMessage($roomId, $orderData, $senderId, $senderName)
    {
        try {
            dispatch(function () use ($roomId, $orderData, $senderId, $senderName) {
                $this->performSendMessage($roomId, $orderData, $senderId, $senderName);
            })->afterResponse();

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to dispatch order message: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Perform the actual message send operation
     */
    protected function performSendMessage($roomId, $orderData, $senderId, $senderName)
    {
        try {
            // Generate unique message ID
            $messageId = Str::random(20);
            
            // Prepare order details text
            $orderText = $this->formatOrderDetails($orderData);
            
            // Prepare message data in Firestore format
            $messageData = [
                'fields' => [
                    'id' => ['stringValue' => $messageId],
                    'sender_id' => ['integerValue' => (string)$senderId],
                    'sender_name' => ['stringValue' => $senderName],
                    'sender_avatar' => ['stringValue' => $orderData['sender_avatar'] ?? ''],
                    'text' => ['stringValue' => $orderText],
                    'type' => ['stringValue' => 'order'],
                    'order_id' => ['integerValue' => (string)$orderData['order_id']],
                    'order_number' => ['integerValue' => (string)$orderData['order_number']],
                    'order_total' => ['doubleValue' => (float)$orderData['total']],
                    'order_status' => ['stringValue' => $orderData['status'] ?? 'pending'],
                    'is_read' => ['booleanValue' => false],
                    'is_delivered' => ['booleanValue' => false],
                    'reply_to' => ['stringValue' => ''],
                    'media_url' => ['stringValue' => ''],
                    'created_at' => ['timestampValue' => now()->toIso8601String()],
                ]
            ];

            // Send message to Firestore
            $response = Http::timeout(10)->patch(
                "{$this->baseUrl}/rooms/room_{$roomId}/messages/{$messageId}",
                $messageData
            );

            if ($response->successful()) {
                Log::info("Order message sent to room {$roomId}");
                
                // Update room's last message
                $this->updateRoomLastMessage($roomId, $orderText);
            } else {
                Log::error("Failed to send order message to Firestore", [
                    'room_id' => $roomId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to send message to Firestore: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Format order details into a readable message
     */
    protected function formatOrderDetails($orderData)
    {
        $text = "📦 New Order Created\n\n";
        $text .= "Order #: {$orderData['order_number']}\n";
        $text .= "Total: {$orderData['currency']}{$orderData['total']}\n";
        $text .= "Items: {$orderData['items_count']}\n";
        $text .= "Payment: {$orderData['payment_type']}\n";
        
        if (isset($orderData['delivery_fee']) && $orderData['delivery_fee'] > 0) {
            $text .= "Delivery Fee: {$orderData['currency']}{$orderData['delivery_fee']}\n";
        }
        
        if (isset($orderData['discount']) && $orderData['discount'] > 0) {
            $text .= "Discount: {$orderData['currency']}{$orderData['discount']}\n";
        }
        
        $text .= "\nStatus: " . ucfirst($orderData['status'] ?? 'pending');
        
        return $text;
    }

    /**
     * Update room's last message information
     */
    protected function updateRoomLastMessage($roomId, $messageText)
    {
        try {
            $updateData = [
                'fields' => [
                    'last_message' => ['stringValue' => $messageText],
                    'last_message_at' => ['timestampValue' => now()->toIso8601String()],
                ]
            ];

            Http::timeout(10)->patch(
                "{$this->baseUrl}/rooms/room_{$roomId}?updateMask.fieldPaths=last_message&updateMask.fieldPaths=last_message_at",
                $updateData
            );
        } catch (\Exception $e) {
            Log::error("Failed to update room last message: " . $e->getMessage());
        }
    }
}