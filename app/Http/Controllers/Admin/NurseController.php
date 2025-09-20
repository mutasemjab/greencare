<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NurseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:nurse-table', ['only' => ['index', 'show']]);
        $this->middleware('permission:nurse-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:nurse-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:nurse-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of nurses
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 'nurse');

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

        $nurses = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.nurses.index', compact('nurses'));
    }

    /**
     * Show the form for creating a new nurse
     */
    public function create()
    {
        return view('admin.nurses.create');
    }

    /**
     * Store a newly created nurse
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
            'department' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'shift_type' => 'nullable|in:morning,evening,night',
            'experience_years' => 'nullable|integer|min:0|max:50'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $userData = [
                'user_type' => 'nurse',
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

            $nurse = User::create($userData);

            // Store additional nurse info in profile or separate table if needed
            // You might want to create a nurse_profiles table for additional fields

            return redirect()->route('nurses.index')
                ->with('success', __('messages.nurse_created_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_creating_nurse'))
                ->withInput();
        }
    }

    /**
     * Display the specified nurse
     */
    public function show(User $nurse)
    {
        // Ensure we're only showing nurses
        if ($nurse->user_type !== 'nurse') {
            abort(404);
        }

        $nurse->load('families');
        return view('admin.nurses.show', compact('nurse'));
    }

    /**
     * Show the form for editing the specified nurse
     */
    public function edit(User $nurse)
    {
        // Ensure we're only editing nurses
        if ($nurse->user_type !== 'nurse') {
            abort(404);
        }

        return view('admin.nurses.edit', compact('nurse'));
    }

    /**
     * Update the specified nurse
     */
    public function update(Request $request, User $nurse)
    {
        // Ensure we're only updating nurses
        if ($nurse->user_type !== 'nurse') {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users')->ignore($nurse->id)
            ],
            'phone' => [
                'required',
                'string',
                Rule::unique('users')->ignore($nurse->id)
            ],
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:1,2',
            'activate' => 'required|in:1,2',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'fcm_token' => 'nullable|string',
            'department' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'shift_type' => 'nullable|in:morning,evening,night',
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

            $nurse->update($userData);

            return redirect()->route('nurses.index')
                ->with('success', __('messages.nurse_updated_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_updating_nurse'))
                ->withInput();
        }
    }

    /**
     * Remove the specified nurse
     */
    public function destroy(User $nurse)
    {
        // Ensure we're only deleting nurses
        if ($nurse->user_type !== 'nurse') {
            abort(404);
        }

        try {
            // Delete photo if exists
            if ($nurse->photo) {
                Storage::disk('public')->delete($nurse->photo);
            }

            $nurse->delete();

            return redirect()->route('nurses.index')
                ->with('success', __('messages.nurse_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_deleting_nurse'));
        }
    }

    /**
     * Toggle nurse activation status
     */
    public function toggleStatus(User $nurse)
    {
        // Ensure we're only toggling nurses
        if ($nurse->user_type !== 'nurse') {
            abort(404);
        }

        try {
            $nurse->update([
                'activate' => $nurse->activate == 1 ? 2 : 1
            ]);

            $status = $nurse->activate == 1 ? __('messages.activated') : __('messages.deactivated');
            return redirect()->back()
                ->with('success', __('messages.nurse_status_updated', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_updating_status'));
        }
    }

    /**
     * Get nurses for API or AJAX calls
     */
    public function getNurses(Request $request)
    {
        $query = User::where('user_type', 'nurse')
                    ->where('activate', 1);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        if ($request->has('shift_type')) {
            $query->where('shift_type', $request->shift_type);
        }

        $nurses = $query->select('id', 'name', 'phone', 'email', 'department', 'shift_type')
                       ->orderBy('name')
                       ->get();

        return response()->json($nurses);
    }
}