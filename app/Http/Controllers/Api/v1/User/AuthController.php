<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\ClassTeacher;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Traits\Responses;
use Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    use Responses;


    // Register
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'nullable|email|unique:users,email',
            'phone'         => 'required|string|unique:users,phone',
            'date_of_birth' => 'required|date',
            'gender'        => 'required|in:1,2',
            'photo'         => 'nullable|string',
            'fcm_token'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::create($validator->validated());

        // Generate token
        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    // Login with phone only
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|exists:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user || $user->activate != 1) {
            return response()->json([
                'status' => false,
                'message' => 'User not found or not active'
            ], 401);
        }

        if ($request->has('fcm_token')) {
            $user->fcm_token = $request->fcm_token;
            $user->save();
        }

        // Generate token
        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }


    public function active()
    {
        $user = auth()->user();
        if ($user->activate == 2) {
            return $this->error_response('Your account has been InActive', null);
        }

        return $this->success_response('User retrieved successfully', $user);
    }

    public function deleteAccount(Request $request)
    {
        try {

            $userApi = auth('user-api')->user();

            if ($userApi) {
                // Deactivate and revoke tokens
                $userApi->update(['activate' => 2]);
                $userApi->tokens()->delete();

                return $this->success_response('User account deleted successfully', null);
            } else {
                return $this->error_response('Unauthenticated', [], 401);
            }
        } catch (\Exception $e) {
            \Log::error('Account deletion error: ' . $e->getMessage());
            return $this->error_response('Failed to delete account', ['error' => $e->getMessage()]);
        }
    }



    public function userProfile()
    {
        try {
            // Check both authentication guards
            $userApi = auth('user-api')->user();

            if ($userApi) {
                // If it's a regular user
                return $this->success_response('User profile retrieved', $userApi);
            } else {
                return $this->error_response('Unauthenticated', [], 401);
            }
        } catch (\Throwable $th) {
            \Log::error('Profile retrieval error: ' . $th->getMessage());
            return $this->error_response('Failed to retrieve profile', []);
        }
    }



    public function updateProfile(Request $request)
    {
        try {
            $user = auth('user-api')->user();

            if (!$user) {
                return $this->error_response('Unauthenticated', [], 401);
            }

            $validationRules = [
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'date_of_birth' => 'nullable',
                'phone' => 'nullable|string',
                'photo' => 'nullable|image',
            ];

            $validator = Validator::make($request->all(), $validationRules);
            if ($validator->fails()) {
                return $this->error_response('Validation error', $validator->errors());
            }

            $data = $request->only(['name', 'email', 'phone', 'date_of_birth']);

            if ($request->hasFile('photo')) {
                if ($user->photo && file_exists('assets/admin/uploads/' . $user->photo)) {
                    unlink('assets/admin/uploads/' . $user->photo);
                }
                $data['photo'] = uploadImage('assets/admin/uploads', $request->file('photo'));
            }

            $user->update($data);

            return $this->success_response('User profile updated successfully', $user);
        } catch (\Throwable $th) {
            \Log::error('User Profile update error: ' . $th->getMessage());
            return $this->error_response('Failed to update profile', ['message' => $th->getMessage()]);
        }
    }
}
