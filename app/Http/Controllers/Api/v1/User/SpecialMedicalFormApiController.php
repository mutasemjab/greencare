<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\SpecialMedicalForm;
use App\Models\SpecialMedicalFormReply;
use App\Models\RoomUser;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SpecialMedicalFormApiController extends Controller
{
    use Responses;

    /**
     * Get all forms in a room
     */
    public function getRoomForms($roomId)
    {
        try {
            $user = Auth::user();

            // Check if user has access to this room
            $hasAccess = RoomUser::where('room_id', $roomId)
                ->where('user_id', $user->id)
                ->exists();

            if (!$hasAccess) {
                return $this->error_response(
                    __('messages.no_access_to_room'),
                    []
                );
            }

            $forms = SpecialMedicalForm::with(['creator', 'replies.user'])
                ->where('room_id', $roomId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($form) {
                    return [
                        'id' => $form->id,
                        'room_id' => $form->room_id,
                        'title' => $form->title,
                        'note' => $form->note,
                        'signature_url' => $form->signature_url,
                        'status' => $form->status,
                        'status_label' => __('messages.form_status_' . $form->status),
                        'created_by' => [
                            'id' => $form->creator->id,
                            'name' => $form->creator->name,
                            'user_type' => $form->creator->user_type,
                            'photo' => $form->creator->photo,
                        ],
                        'replies_count' => $form->replies->count(),
                        'created_at' => $form->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $form->updated_at->format('Y-m-d H:i:s'),
                    ];
                });

            return $this->success_response(
                __('messages.forms_retrieved_successfully'),
                ['forms' => $forms]
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_occurred'),
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Create a new special medical form
     */
    public function createForm(Request $request)
    {
        try {
            $user = Auth::user();

            // Only doctors and nurses can create forms
            if (!in_array($user->user_type, ['doctor', 'nurse'])) {
                return $this->error_response(
                    __('messages.unauthorized_action'),
                    []
                );
            }

            $validator = Validator::make($request->all(), [
                'room_id' => 'required|exists:rooms,id',
                'title' => 'required|string|max:255',
                'note' => 'required|string',
                'signature' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            // Check if user has access to this room
            $hasAccess = RoomUser::where('room_id', $request->room_id)
                ->where('user_id', $user->id)
                ->whereIn('role', ['doctor', 'nurse'])
                ->exists();

            if (!$hasAccess) {
                return $this->error_response(
                    __('messages.no_access_to_room'),
                    []
                );
            }

            // Upload signature
            $signaturePath = uploadImage('assets/admin/uploads', $request->signature);

            // Create form
            $form = SpecialMedicalForm::create([
                'room_id' => $request->room_id,
                'created_by' => $user->id,
                'title' => $request->title,
                'note' => $request->note,
                'signature_path' => $signaturePath,
                'status' => 'open',
            ]);

            $form->load(['creator', 'replies']);

            return $this->success_response(
                __('messages.form_created_successfully'),
                [
                    'form' => [
                        'id' => $form->id,
                        'room_id' => $form->room_id,
                        'title' => $form->title,
                        'note' => $form->note,
                        'signature_url' => $form->signature_url,
                        'status' => $form->status,
                        'created_by' => [
                            'id' => $form->creator->id,
                            'name' => $form->creator->name,
                            'user_type' => $form->creator->user_type,
                            'photo' => $form->creator->photo,
                        ],
                        'replies_count' => 0,
                        'created_at' => $form->created_at->format('Y-m-d H:i:s'),
                    ]
                ]
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_creating_form'),
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Get form details with all replies
     */
    public function getFormDetails($formId)
    {
        try {
            $user = Auth::user();

            $form = SpecialMedicalForm::with(['creator', 'room', 'replies.user'])
                ->find($formId);

            if (!$form) {
                return $this->error_response(
                    __('messages.form_not_found'),
                    []
                );
            }

            // Check if user has access to this room
            $hasAccess = RoomUser::where('room_id', $form->room_id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$hasAccess) {
                return $this->error_response(
                    __('messages.no_access_to_room'),
                    []
                );
            }

            $formData = [
                'id' => $form->id,
                'room_id' => $form->room_id,
                'room_title' => $form->room->title,
                'title' => $form->title,
                'note' => $form->note,
                'signature_url' => $form->signature_url,
                'status' => $form->status,
                'status_label' => __('messages.form_status_' . $form->status),
                'created_by' => [
                    'id' => $form->creator->id,
                    'name' => $form->creator->name,
                    'user_type' => $form->creator->user_type,
                    'photo' => $form->creator->photo,
                ],
                'created_at' => $form->created_at->format('Y-m-d H:i:s'),
                'replies' => $form->replies->map(function ($reply) {
                    return [
                        'id' => $reply->id,
                        'note' => $reply->note,
                        'signature_url' => $reply->signature_url,
                        'user' => [
                            'id' => $reply->user->id,
                            'name' => $reply->user->name,
                            'user_type' => $reply->user->user_type,
                            'photo' => $reply->user->photo,
                        ],
                        'created_at' => $reply->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
            ];

            return $this->success_response(
                __('messages.form_details_retrieved_successfully'),
                ['form' => $formData]
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_occurred'),
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Reply to a form
     */
    public function replyToForm(Request $request, $formId)
    {
        try {
            $user = Auth::user();

            // Only doctors and nurses can reply
            if (!in_array($user->user_type, ['doctor', 'nurse'])) {
                return $this->error_response(
                    __('messages.unauthorized_action'),
                    []
                );
            }

            $form = SpecialMedicalForm::find($formId);

            if (!$form) {
                return $this->error_response(
                    __('messages.form_not_found'),
                    []
                );
            }

            // Check if form is closed
            if ($form->status === 'closed') {
                return $this->error_response(
                    __('messages.form_is_closed'),
                    []
                );
            }

            // Check if user has access to this room
            $hasAccess = RoomUser::where('room_id', $form->room_id)
                ->where('user_id', $user->id)
                ->whereIn('role', ['doctor', 'nurse'])
                ->exists();

            if (!$hasAccess) {
                return $this->error_response(
                    __('messages.no_access_to_room'),
                    []
                );
            }

            $validator = Validator::make($request->all(), [
                'note' => 'required|string',
                'signature' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            // Upload signature
            $signaturePath = uploadImage('assets/admin/uploads', $request->signature);

            // Create reply
            $reply = SpecialMedicalFormReply::create([
                'special_medical_form_id' => $form->id,
                'user_id' => $user->id,
                'note' => $request->note,
                'signature_path' => $signaturePath,
            ]);

            $reply->load('user');

            return $this->success_response(
                __('messages.reply_added_successfully'),
                [
                    'reply' => [
                        'id' => $reply->id,
                        'note' => $reply->note,
                        'signature_url' => $reply->signature_url,
                        'user' => [
                            'id' => $reply->user->id,
                            'name' => $reply->user->name,
                            'user_type' => $reply->user->user_type,
                            'photo' => $reply->user->photo,
                        ],
                        'created_at' => $reply->created_at->format('Y-m-d H:i:s'),
                    ]
                ]
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_adding_reply'),
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Update form status (open/closed)
     */
    public function updateFormStatus(Request $request, $formId)
    {
        try {
            $user = Auth::user();

            $form = SpecialMedicalForm::find($formId);

            if (!$form) {
                return $this->error_response(
                    __('messages.form_not_found'),
                    []
                );
            }

            // Only the creator can close the form
            if ($form->created_by !== $user->id) {
                return $this->error_response(
                    __('messages.only_creator_can_close_form'),
                    []
                );
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:open,closed',
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            $form->update(['status' => $request->status]);

            return $this->success_response(
                __('messages.form_status_updated_successfully'),
                [
                    'form' => [
                        'id' => $form->id,
                        'status' => $form->status,
                        'status_label' => __('messages.form_status_' . $form->status),
                    ]
                ]
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_occurred'),
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Delete a form (only creator can delete if no replies)
     */
    public function deleteForm($formId)
    {
        try {
            $user = Auth::user();

            $form = SpecialMedicalForm::withCount('replies')->find($formId);

            if (!$form) {
                return $this->error_response(
                    __('messages.form_not_found'),
                    []
                );
            }

            // Only the creator can delete
            if ($form->created_by !== $user->id) {
                return $this->error_response(
                    __('messages.only_creator_can_delete_form'),
                    []
                );
            }

            // Cannot delete if there are replies
            if ($form->replies_count > 0) {
                return $this->error_response(
                    __('messages.cannot_delete_form_with_replies'),
                    []
                );
            }

            // Delete signature file
            if ($form->signature_path) {
                $filePath = base_path('assets/admin/uploads/' . $form->signature_path);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $form->delete();

            return $this->success_response(
                __('messages.form_deleted_successfully'),
                []
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_occurred'),
                ['error' => $e->getMessage()]
            );
        }
    }
}