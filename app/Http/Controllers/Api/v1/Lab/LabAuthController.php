<?php

namespace App\Http\Controllers\Api\v1\Lab;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Traits\Responses;
use Illuminate\Support\Facades\Validator;

class LabAuthController extends Controller
{
    use Responses;

    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Send OTP to phone number
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
        ]);

        $fullPhone = $request->phone_number;
        $otpResult = $this->otpService->sendOTP($fullPhone);

        if ($otpResult['success']) {
            return $this->success_response('OTP sent successfully', [
                'debug_otp' => $otpResult['otp'] ?? null,
            ]);
        }

        return $this->error_response($otpResult['message'], $otpResult['error'] ?? null);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'otp' => 'required|string',
        ]);

        $fullPhone = $request->phone_number;
        $otpResult = $this->otpService->verifyOTPWithTestCase($fullPhone, $request->otp);

        if ($otpResult['success']) {
            return $this->success_response('OTP verified successfully', []);
        }

        return $this->error_response($otpResult['message'], $otpResult['error_code'] ?? null);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
        ]);

        $fullPhone = $request->phone_number;
        $otpResult = $this->otpService->sendOTP($fullPhone);

        if ($otpResult['success']) {
            return $this->success_response('OTP resent successfully', [
                'debug_otp' => $otpResult['otp'] ?? null,
            ]);
        }

        return $this->error_response($otpResult['message'], $otpResult['error'] ?? null);
    }

    /**
     * Update FCM Token
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $lab = auth()->user();
        $lab->update([
            'fcm_token' => $request->fcm_token,
        ]);

        return $this->success_response('FCM token updated successfully', [
            'fcm_token' => $lab->fcm_token
        ]);
    }

    /**
     * Register Lab
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:labs,email',
            'phone' => 'required|string|unique:labs,phone',
            'license_number' => 'nullable|string|unique:labs,license_number',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'photo' => 'nullable|string',
            'fcm_token' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $lab = Lab::create($validator->validated());

        // Generate token
        $token = $lab->createToken('authToken')->accessToken;

        return response()->json([
            'status' => true,
            'message' => 'Lab registered successfully',
            'data' => [
                'lab' => $lab,
                'token' => $token
            ]
        ]);
    }

    /**
     * Login with phone only
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|exists:labs,phone',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $lab = Lab::where('phone', $request->phone)->first();

        if (!$lab || $lab->activate != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Lab not found or not active'
            ], 401);
        }

        if ($request->has('fcm_token')) {
            $lab->fcm_token = $request->fcm_token;
            $lab->save();
        }

        // Generate token
        $token = $lab->createToken('authToken')->accessToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'lab' => $lab,
                'token' => $token
            ]
        ]);
    }

    /**
     * Check if lab is active
     */
    public function active()
    {
        $lab = auth()->user();
        if ($lab->activate == 2) {
            return $this->error_response('Your account has been inactive', null);
        }

        return $this->success_response('Lab retrieved successfully', $lab);
    }

    /**
     * Delete Lab Account
     */
    public function deleteAccount(Request $request)
    {
        try {
            $lab = auth('lab-api')->user();

            if ($lab) {
                // Deactivate and revoke tokens
                $lab->update(['activate' => 2]);
                $lab->tokens()->delete();

                return $this->success_response('Lab account deleted successfully', null);
            } else {
                return $this->error_response('Unauthenticated', [], 401);
            }
        } catch (\Exception $e) {
            \Log::error('Account deletion error: ' . $e->getMessage());
            return $this->error_response('Failed to delete account', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get Lab Profile
     */
    public function labProfile()
    {
        try {
            $lab = auth('lab-api')->user();

            if ($lab) {
                return $this->success_response('Lab profile retrieved', $lab);
            } else {
                return $this->error_response('Unauthenticated', [], 401);
            }
        } catch (\Throwable $th) {
            \Log::error('Profile retrieval error: ' . $th->getMessage());
            return $this->error_response('Failed to retrieve profile', []);
        }
    }

    /**
     * Update Lab Profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $lab = auth('lab-api')->user();

            if (!$lab) {
                return $this->error_response('Unauthenticated', [], 401);
            }

            $validationRules = [
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:labs,email,' . $lab->id,
                'phone' => 'nullable|string|unique:labs,phone,' . $lab->id,
                'license_number' => 'nullable|string|unique:labs,license_number,' . $lab->id,
                'address' => 'nullable|string',
                'description' => 'nullable|string',
                'photo' => 'nullable|image',
            ];

            $validator = Validator::make($request->all(), $validationRules);
            if ($validator->fails()) {
                return $this->error_response('Validation error', $validator->errors());
            }

            $data = $request->only(['name', 'email', 'phone', 'license_number', 'address', 'description']);

            if ($request->hasFile('photo')) {
                if ($lab->photo && file_exists('assets/admin/uploads/' . $lab->photo)) {
                    unlink('assets/admin/uploads/' . $lab->photo);
                }
                $data['photo'] = uploadImage('assets/admin/uploads', $request->file('photo'));
            }

            $lab->update($data);

            return $this->success_response('Lab profile updated successfully', $lab);
        } catch (\Throwable $th) {
            \Log::error('Lab Profile update error: ' . $th->getMessage());
            return $this->error_response('Failed to update profile', ['message' => $th->getMessage()]);
        }
    }
}