<?php

namespace App\Http\Controllers\Api\v1\Lab;

use App\Http\Controllers\Controller;
use App\Models\Lab;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LabAuthController extends Controller
{
    use Responses;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error_response('Validation failed', $validator->errors());
        }

        $lab = Lab::where('phone', $request->phone)->first();

        if (!$lab || !Hash::check($request->password, $lab->password)) {
            return $this->error_response('Invalid credentials', null, 401);
        }

        if ($lab->activate != 1) {
            return $this->error_response('Your account is not active', null, 403);
        }

        $token = $lab->createToken('lab-token')->accessToken;

        return $this->success_response('Login successful', [
            'lab'   => $lab,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->success_response('Logged out successfully', null);
    }

    public function profile(Request $request)
    {
        return $this->success_response('Profile retrieved', Auth::guard('lab-api')->user());
    }
}
