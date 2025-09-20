<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DoctorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:doctor-table', ['only' => ['index', 'show']]);
        $this->middleware('permission:doctor-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:doctor-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:doctor-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of doctors
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 'doctor');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by gender
        if ($request->has('gender') && $request->gender != '') {
            $query->where('gender', $request->gender);
        }

        // Filter by status
        if ($request->has('activate') && $request->activate != '') {
            $query->where('activate', $request->activate);
        }

        $doctors = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.doctors.index', compact('doctors'));
    }

    /**
     * Show the form for creating a new doctor
     */
    public function create()
    {
        return view('admin.doctors.create');
    }

    /**
     * Store a newly created doctor
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:1,2',
            'activate' => 'required|in:1,2',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'fcm_token' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:50'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $userData = [
                'user_type' => 'doctor',
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'activate' => $request->activate,
                'fcm_token' => $request->fcm_token,
            ];

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $path = uploadImage('assets/admin/uploads', $request->photo);
                $userData['photo'] = $path;
            }

            $doctor = User::create($userData);

            // Store additional doctor info in profile or separate table if needed
            // You might want to create a doctor_profiles table for additional fields

            return redirect()->route('doctors.index')
                ->with('success', __('messages.doctor_created_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_creating_doctor'))
                ->withInput();
        }
    }

    /**
     * Display the specified doctor
     */
    public function show(User $doctor)
    {
        // Ensure we're only showing doctors
        if ($doctor->user_type !== 'doctor') {
            abort(404);
        }

        $doctor->load('families');
        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified doctor
     */
    public function edit(User $doctor)
    {
        // Ensure we're only editing doctors
        if ($doctor->user_type !== 'doctor') {
            abort(404);
        }

        return view('admin.doctors.edit', compact('doctor'));
    }

    /**
     * Update the specified doctor
     */
    public function update(Request $request, User $doctor)
    {
        // Ensure we're only updating doctors
        if ($doctor->user_type !== 'doctor') {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users')->ignore($doctor->id)
            ],
            'phone' => [
                'required',
                'string',
                Rule::unique('users')->ignore($doctor->id)
            ],
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:1,2',
            'activate' => 'required|in:1,2',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'fcm_token' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0|max:50'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'activate' => $request->activate,
                'fcm_token' => $request->fcm_token,
            ];

            // Handle photo upload
            if ($request->hasFile('photo')) {
               $path = uploadImage('assets/admin/uploads', $request->photo);
                $userData['photo'] = $path;
            }

            $doctor->update($userData);

            return redirect()->route('doctors.index')
                ->with('success', __('messages.doctor_updated_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_updating_doctor'))
                ->withInput();
        }
    }

    /**
     * Remove the specified doctor
     */
    public function destroy(User $doctor)
    {
        // Ensure we're only deleting doctors
        if ($doctor->user_type !== 'doctor') {
            abort(404);
        }

        try {
            // Delete photo if exists
            if ($doctor->photo) {
                Storage::disk('public')->delete($doctor->photo);
            }

            $doctor->delete();

            return redirect()->route('doctors.index')
                ->with('success', __('messages.doctor_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_deleting_doctor'));
        }
    }

    /**
     * Toggle doctor activation status
     */
    public function toggleStatus(User $doctor)
    {
        // Ensure we're only toggling doctors
        if ($doctor->user_type !== 'doctor') {
            abort(404);
        }

        try {
            $doctor->update([
                'activate' => $doctor->activate == 1 ? 2 : 1
            ]);

            $status = $doctor->activate == 1 ? __('messages.activated') : __('messages.deactivated');
            return redirect()->back()
                ->with('success', __('messages.doctor_status_updated', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_updating_status'));
        }
    }

    /**
     * Get doctors for API or AJAX calls
     */
    public function getDoctors(Request $request)
    {
        $query = User::where('user_type', 'doctor')
                    ->where('activate', 1);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $doctors = $query->select('id', 'name', 'phone', 'email')
                        ->orderBy('name')
                        ->get();

        return response()->json($doctors);
    }
}