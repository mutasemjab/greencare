<?php

namespace App\Http\Controllers\Lab\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('lab.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        $credentials = [
            'phone' => $request->phone,
            'password' => $request->password,
            'activate' => 1, // التأكد من أن المختبر مفعّل
        ];

        if (Auth::guard('lab')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('lab.dashboard'));
        }

        return back()->withErrors([
            'phone' => 'بيانات الدخول غير صحيحة.',
        ])->withInput($request->only('phone'));
    }

    public function logout(Request $request)
    {
        Auth::guard('lab')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('lab.login');
    }
}