<?php

namespace App\Services;

use App\Models\Room;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirestoreRoomService
{
    protected $projectId;
    protected $baseUrl;
    protected $collection = 'rooms';

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    }

    /**
     * Create or update room in Firestore using REST API
     * 
     * @param Room $room
     * @return bool
     */
    public function syncRoom(Room $room)
    {
        try {
            // Use afterResponse to avoid blocking
            dispatch(function () use ($room) {
                $this->performSync($room);
            })->afterResponse();

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to dispatch room sync: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Perform the actual sync operation using REST API
     */
    protected function performSync(Room $room)
    {
        try {
            // Load room relationships
            $room->load(['users', 'family']);

            // Prepare users data
            $users = $room->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email ?? '',
                    'phone' => $user->phone,
                    'photo' => $user->photo ?? '',
                    'user_type' => $user->user_type,
                    'role' => $user->pivot->role,
                    'fcm_token' => $user->fcm_token ?? '',
                    'gender' => $user->gender,
                    'date_of_birth' => $user->date_of_birth,
                ];
            })->toArray();

            // Group users by role
            $usersByRole = [
                'patients' => array_values(array_filter($users, fn($u) => $u['role'] === 'patient')),
                'doctors' => array_values(array_filter($users, fn($u) => $u['role'] === 'doctor')),
                'nurses' => array_values(array_filter($users, fn($u) => $u['role'] === 'nurse')),
                'family' => array_values(array_filter($users, fn($u) => $u['role'] === 'family')),
            ];

            // Convert to Firestore REST API format
            $roomData = [
                'fields' => [
                    'id' => ['integerValue' => (string)$room->id],
                    'title' => ['stringValue' => $room->title],
                    'description' => ['stringValue' => $room->description ?? ''],
                    'discount' => ['doubleValue' => (float)$room->discount],
                    'family_id' => $room->family_id ? ['integerValue' => (string)$room->family_id] : ['nullValue' => null],
                    'family_name' => ['stringValue' => $room->family ? $room->family->name : ''],
                    'users' => $this->convertArrayToFirestore($users),
                    'users_by_role' => $this->convertMapToFirestore($usersByRole),
                    'user_ids' => $this->convertSimpleArrayToFirestore(array_column($users, 'id')),
                    'total_users' => ['integerValue' => (string)count($users)],
                    'created_at' => ['timestampValue' => $room->created_at->toIso8601String()],
                    'updated_at' => ['timestampValue' => $room->updated_at->toIso8601String()],
                    'last_message' => ['stringValue' => ''],
                    'last_message_at' => ['timestampValue' => now()->toIso8601String()],
                    'unread_count' => ['integerValue' => '0'],
                ]
            ];

            // Send to Firestore using REST API
            $response = Http::timeout(10)->patch(
                "{$this->baseUrl}/{$this->collection}/room_{$room->id}",
                $roomData
            );

            if ($response->successful()) {
                Log::info("Room synced to Firestore: Room ID {$room->id}");
            } else {
                Log::error("Failed to sync room to Firestore", [
                    'room_id' => $room->id,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to sync room to Firestore: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Convert PHP array to Firestore array format
     */
    protected function convertArrayToFirestore(array $items): array
    {
        $values = [];
        foreach ($items as $item) {
            $fields = [];
            foreach ($item as $key => $value) {
                if (is_int($value)) {
                    $fields[$key] = ['integerValue' => (string)$value];
                } elseif (is_string($value)) {
                    $fields[$key] = ['stringValue' => $value];
                } elseif (is_bool($value)) {
                    $fields[$key] = ['booleanValue' => $value];
                } elseif (is_null($value)) {
                    $fields[$key] = ['nullValue' => null];
                } else {
                    $fields[$key] = ['stringValue' => (string)$value];
                }
            }
            $values[] = ['mapValue' => ['fields' => $fields]];
        }
        
        return ['arrayValue' => ['values' => $values]];
    }

    /**
     * Convert PHP associative array to Firestore map format
     */
    protected function convertMapToFirestore(array $map): array
    {
        $fields = [];
        foreach ($map as $key => $value) {
            if (is_array($value)) {
                $fields[$key] = $this->convertArrayToFirestore($value);
            } else {
                $fields[$key] = ['stringValue' => (string)$value];
            }
        }
        
        return ['mapValue' => ['fields' => $fields]];
    }

    /**
     * Convert simple array (like IDs) to Firestore array format
     */
    protected function convertSimpleArrayToFirestore(array $items): array
    {
        $values = array_map(function($item) {
            return ['integerValue' => (string)$item];
        }, $items);
        
        return ['arrayValue' => ['values' => $values]];
    }

    /**
     * Delete room from Firestore
     * 
     * @param int $roomId
     * @return bool
     */
    public function deleteRoom($roomId)
    {
        try {
            dispatch(function () use ($roomId) {
                try {
                    $response = Http::timeout(10)->delete(
                        "{$this->baseUrl}/{$this->collection}/room_{$roomId}"
                    );

                    if ($response->successful()) {
                        Log::info("Room deleted from Firestore: Room ID {$roomId}");
                    } else {
                        Log::error("Failed to delete room from Firestore", [
                            'room_id' => $roomId,
                            'status' => $response->status()
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to delete room from Firestore: " . $e->getMessage());
                }
            })->afterResponse();

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to dispatch room deletion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user's FCM token in all their rooms
     * 
     * @param int $userId
     * @param string $fcmToken
     * @return bool
     */
    public function updateUserFcmToken($userId, $fcmToken)
    {
        try {
            dispatch(function () use ($userId, $fcmToken) {
                try {
                    // Query all rooms where this user is a member
                    $response = Http::timeout(10)->post("{$this->baseUrl}:runQuery", [
                        'structuredQuery' => [
                            'from' => [['collectionId' => $this->collection]],
                            'where' => [
                                'fieldFilter' => [
                                    'field' => ['fieldPath' => 'user_ids'],
                                    'op' => 'ARRAY_CONTAINS',
                                    'value' => ['integerValue' => (string)$userId]
                                ]
                            ]
                        ]
                    ]);

                    if ($response->successful()) {
                        $results = $response->json();
                        
                        foreach ($results as $result) {
                            if (isset($result['document'])) {
                                $roomId = basename($result['document']['name']);
                                // For simplicity, just resync the entire room
                                $room = Room::find(str_replace('room_', '', $roomId));
                                if ($room) {
                                    $this->performSync($room);
                                }
                            }
                        }

                        Log::info("Updated FCM token for user {$userId} in all rooms");
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to update FCM token: " . $e->getMessage());
                }
            })->afterResponse();

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to dispatch FCM token update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add user to room in Firestore
     */
    public function addUserToRoom(Room $room)
    {
        return $this->syncRoom($room);
    }

    /**
     * Remove user from room in Firestore
     */
    public function removeUserFromRoom(Room $room)
    {
        return $this->syncRoom($room);
    }

    /**
     * Get room from Firestore
     */
    public function getRoom($roomId)
    {
        try {
            $response = Http::timeout(10)->get(
                "{$this->baseUrl}/{$this->collection}/room_{$roomId}"
            );

            if ($response->successful()) {
                return $this->parseFirestoreDocument($response->json());
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Failed to get room from Firestore: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all rooms for a specific user
     */
    public function getUserRooms($userId)
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}:runQuery", [
                'structuredQuery' => [
                    'from' => [['collectionId' => $this->collection]],
                    'where' => [
                        'fieldFilter' => [
                            'field' => ['fieldPath' => 'user_ids'],
                            'op' => 'ARRAY_CONTAINS',
                            'value' => ['integerValue' => (string)$userId]
                        ]
                    ]
                ]
            ]);

            $rooms = [];
            if ($response->successful()) {
                $results = $response->json();
                
                foreach ($results as $result) {
                    if (isset($result['document'])) {
                        $rooms[] = $this->parseFirestoreDocument($result['document']);
                    }
                }
            }

            return $rooms;
        } catch (\Exception $e) {
            Log::error("Failed to get user rooms from Firestore: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Parse Firestore document to PHP array
     */
    protected function parseFirestoreDocument($document)
    {
        $fields = $document['fields'] ?? [];
        $parsed = [];

        foreach ($fields as $key => $value) {
            if (isset($value['stringValue'])) {
                $parsed[$key] = $value['stringValue'];
            } elseif (isset($value['integerValue'])) {
                $parsed[$key] = (int)$value['integerValue'];
            } elseif (isset($value['doubleValue'])) {
                $parsed[$key] = (float)$value['doubleValue'];
            } elseif (isset($value['booleanValue'])) {
                $parsed[$key] = $value['booleanValue'];
            } elseif (isset($value['arrayValue'])) {
                $parsed[$key] = $this->parseFirestoreArray($value['arrayValue']);
            } elseif (isset($value['mapValue'])) {
                $parsed[$key] = $this->parseFirestoreMap($value['mapValue']);
            }
        }

        return $parsed;
    }

    protected function parseFirestoreArray($arrayValue)
    {
        $result = [];
        if (isset($arrayValue['values'])) {
            foreach ($arrayValue['values'] as $value) {
                if (isset($value['mapValue'])) {
                    $result[] = $this->parseFirestoreMap($value['mapValue']);
                } elseif (isset($value['stringValue'])) {
                    $result[] = $value['stringValue'];
                } elseif (isset($value['integerValue'])) {
                    $result[] = (int)$value['integerValue'];
                }
            }
        }
        return $result;
    }

    protected function parseFirestoreMap($mapValue)
    {
        $result = [];
        if (isset($mapValue['fields'])) {
            foreach ($mapValue['fields'] as $key => $value) {
                if (isset($value['stringValue'])) {
                    $result[$key] = $value['stringValue'];
                } elseif (isset($value['integerValue'])) {
                    $result[$key] = (int)$value['integerValue'];
                } elseif (isset($value['arrayValue'])) {
                    $result[$key] = $this->parseFirestoreArray($value['arrayValue']);
                }
            }
        }
        return $result;
    }
}