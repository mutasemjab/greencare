<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SuperNurseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:super-nurse-table', ['only' => ['index', 'show']]);
        $this->middleware('permission:super-nurse-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:super-nurse-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:super-nurse-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of super nurses
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 'super_nurse');

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

        $superNurses = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.super-nurses.index', compact('superNurses'));
    }

    /**
     * Show the form for creating a new super nurse
     */
    public function create()
    {
        return view('admin.super-nurses.create');
    }

    /**
     * Store a newly created super nurse
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $userData = [
                'user_type' => 'super_nurse',
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

            $superNurse = User::create($userData);

            return redirect()->route('super-nurses.index')
                ->with('success', __('messages.super_nurse_created_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_creating_super_nurse'))
                ->withInput();
        }
    }

    /**
     * Display the specified super nurse
     */
    public function show(User $superNurse)
    {
        // Ensure we're only showing super nurses
        if ($superNurse->user_type !== 'super_nurse') {
            abort(404);
        }

        $superNurse->load('families');
        return view('admin.super-nurses.show', compact('superNurse'));
    }

    /**
     * Show the form for editing the specified super nurse
     */
    public function edit(User $superNurse)
    {
        // Ensure we're only editing super nurses
        if ($superNurse->user_type !== 'super_nurse') {
            abort(404);
        }

        return view('admin.super-nurses.edit', compact('superNurse'));
    }

    /**
     * Update the specified super nurse
     */
    public function update(Request $request, User $superNurse)
    {
        // Ensure we're only updating super nurses
        if ($superNurse->user_type !== 'super_nurse') {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users')->ignore($superNurse->id)
            ],
            'phone' => [
                'required',
                'string',
                Rule::unique('users')->ignore($superNurse->id)
            ],
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:1,2',
            'activate' => 'required|in:1,2',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'fcm_token' => 'nullable|string',
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

            $superNurse->update($userData);

            return redirect()->route('super-nurses.index')
                ->with('success', __('messages.super_nurse_updated_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_updating_super_nurse'))
                ->withInput();
        }
    }

    /**
     * Remove the specified super nurse
     */
    public function destroy(User $superNurse)
    {
        // Ensure we're only deleting super nurses
        if ($superNurse->user_type !== 'super_nurse') {
            abort(404);
        }

        try {
            // Delete photo if exists
            if ($superNurse->photo) {
                Storage::disk('public')->delete($superNurse->photo);
            }

            $superNurse->delete();

            return redirect()->route('super-nurses.index')
                ->with('success', __('messages.super_nurse_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_deleting_super_nurse'));
        }
    }

    /**
     * Toggle super nurse activation status
     */
    public function toggleStatus(User $superNurse)
    {
        // Ensure we're only toggling super nurses
        if ($superNurse->user_type !== 'super_nurse') {
            abort(404);
        }

        try {
            $superNurse->update([
                'activate' => $superNurse->activate == 1 ? 2 : 1
            ]);

            $status = $superNurse->activate == 1 ? __('messages.activated') : __('messages.deactivated');
            return redirect()->back()
                ->with('success', __('messages.super_nurse_status_updated', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_updating_status'));
        }
    }

    /**
     * Get super nurses for API or AJAX calls
     */
    public function getSuperNurses(Request $request)
    {
        $query = User::where('user_type', 'super_nurse')
                    ->where('activate', 1);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $superNurses = $query->select('id', 'name', 'phone', 'email')
                       ->orderBy('name')
                       ->get();

        return response()->json($superNurses);
    }
}