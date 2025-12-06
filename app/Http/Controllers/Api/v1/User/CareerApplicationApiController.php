<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\CareerApplication;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CareerApplicationApiController extends Controller
{
    use Responses;

    /**
     * Get all active careers
     */
    public function getCareers()
    {
        try {
            $careers = Career::where('is_active', true)
                ->orderBy('title')
                ->get()
                ->map(function ($career) {
                    return [
                        'id' => $career->id,
                        'title' => $career->title,
                        'description' => $career->description,
                    ];
                });

            return $this->success_response(
                __('messages.careers_retrieved_successfully'),
                ['careers' => $careers]
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_occurred'),
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Submit career application
     */
    public function submitApplication(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'career_id' => 'required|exists:careers,id',
                'cv' => 'required|file|mimes:pdf,doc,docx|max:5120', // Max 5MB
                'cover_letter' => 'required|string|max:2000',
            ]);

            if ($validator->fails()) {
                return $this->error_response(
                    __('messages.validation_error'),
                    ['errors' => $validator->errors()]
                );
            }

            $userId = Auth::id();
            if (!$userId) {
                return $this->error_response(
                    __('messages.user_not_authenticated'),
                    []
                );
            }

            // Check if user already applied for this career
            $existingApplication = CareerApplication::where('user_id', $userId)
                ->where('career_id', $request->career_id)
                ->first();

            if ($existingApplication) {
                return $this->error_response(
                    __('messages.already_applied_for_this_career'),
                    []
                );
            }

            // Upload CV
            $cvPath = uploadImage('assets/admin/uploads', $request->cv);

            // Create application
            $application = CareerApplication::create([
                'career_id' => $request->career_id,
                'user_id' => $userId,
                'cv_path' => $cvPath,
                'cover_letter' => $request->cover_letter,
                'status' => 'pending',
            ]);

            $application->load(['career', 'user']);

            return $this->success_response(
                __('messages.application_submitted_successfully'),
                [
                    'application' => [
                        'id' => $application->id,
                        'career' => [
                            'id' => $application->career->id,
                            'title' => $application->career->title,
                        ],
                        'cv_url' => $application->cv_url,
                        'cover_letter' => $application->cover_letter,
                        'status' => $application->status,
                        'status_label' => __('messages.status_' . $application->status),
                        'submitted_at' => $application->created_at->format('Y-m-d H:i:s'),
                    ]
                ]
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_submitting_application'),
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Get user's career applications
     */
    public function myApplications()
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return $this->error_response(
                    __('messages.user_not_authenticated'),
                    []
                );
            }

            $applications = CareerApplication::with(['career'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($application) {
                    return [
                        'id' => $application->id,
                        'career' => [
                            'id' => $application->career->id,
                            'title' => $application->career->title,
                        ],
                        'cv_url' => $application->cv_url,
                        'cover_letter' => $application->cover_letter,
                        'status' => $application->status,
                        'status_label' => __('messages.status_' . $application->status),
                        'submitted_at' => $application->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return $this->success_response(
                __('messages.applications_retrieved_successfully'),
                ['applications' => $applications]
            );

        } catch (\Exception $e) {
            return $this->error_response(
                __('messages.error_occurred'),
                ['error' => $e->getMessage()]
            );
        }
    }
}