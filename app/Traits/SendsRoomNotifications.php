<?php

namespace App\Traits;

use App\Models\User;
use App\Models\RoomUser;
use App\Models\Notification;
use App\Http\Controllers\Admin\FCMController;

trait SendsRoomNotifications
{
    /**
     * Send notifications to all users in the room
     */
    protected function sendRoomCreatedNotifications($room, $userIds = null)
    {
        try {
            // If userIds not provided, get all room users
            if ($userIds === null) {
                $userIds = $room->users()->pluck('users.id');
            }

            $creator = auth()->user();
            $creatorId = $creator ? $creator->id : null;
            $creatorName = $creator ? $creator->name : 'الإدارة';
            
            $title = '🏥 تم إضافتك إلى غرفة جديدة';
            
            // Remove duplicates
            $uniqueUserIds = collect($userIds)->unique();
            
            foreach ($uniqueUserIds as $userId) {
                // Don't send notification to the creator
                if ($userId == $creatorId) {
                    continue;
                }
                
                $user = User::find($userId);
                
                if ($user) {
                    // Get user role in the room
                    $roomUser = RoomUser::where('room_id', $room->id)
                        ->where('user_id', $userId)
                        ->first();
                    
                    $roleText = $this->getRoleText($roomUser->role ?? 'member');
                    $body = "تم إضافتك كـ {$roleText} في الغرفة: {$room->title}";
                    
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
                            'room_created'
                        );
                    }
                }
            }
            
            \Log::info("Room created notifications sent for room ID: {$room->id}");
            
        } catch (\Exception $e) {
            \Log::error('Error sending room created notifications: ' . $e->getMessage());
            // Don't throw exception to avoid affecting room creation
        }
    }

    /**
     * Get role text in Arabic
     */
    protected function getRoleText($role)
    {
        $roles = [
            'patient' => 'مريض',
            'doctor' => 'طبيب',
            'nurse' => 'ممرض/ممرضة',
            'super_nurse' => 'ممرض رئيسي',
            'member' => 'عضو'
        ];
        
        return $roles[$role] ?? 'عضو';
    }
}