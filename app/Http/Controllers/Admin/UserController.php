<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user-table', ['only' => ['index', 'show']]);
        $this->middleware('permission:user-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function searchFamilies(Request $request)
    {
        $search = $request->get('search');
        $perPage = 10;

        $query = Family::with(['users' => function($q) {
            $q->where('user_type', 'patient')
            ->where('activate', 1)
            ->select('users.id', 'users.name', 'users.phone', 'users.photo', 'users.gender');
        }]);

        // Search by family name
        if (!empty($search)) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $families = $query->orderBy('name')
                        ->paginate($perPage);

        // Transform data for Select2
        $data = $families->getCollection()->map(function($family) {
            return [
                'id' => $family->id,
                'name' => $family->name,
                'members_count' => $family->users->count(),
                'members' => $family->users->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone' => $user->phone,
                        'gender_text' => $user->gender_text,
                        'photo_url' => $user->photo ? asset('storage/' . $user->photo) : null,
                    ];
                }),
            ];
        });

        return response()->json([
            'data' => $data,
            'current_page' => $families->currentPage(),
            'last_page' => $families->lastPage(),
            'per_page' => $families->perPage(),
            'total' => $families->total(),
        ]);
    }
    public function searchPatients(Request $request)
    {
        $search = $request->get('search');
        $familyId = $request->get('family_id');
        $perPage = 10;

        $query = User::where('user_type', 'patient')
                    ->where('activate', 1);

        // Exclude users already in the family
        if ($familyId) {
            $query->whereDoesntHave('families', function($q) use ($familyId) {
                $q->where('families.id', $familyId);
            });
        }

        // Search by name or phone
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $patients = $query->orderBy('name')
                        ->paginate($perPage);

        // Transform data for Select2
        $data = $patients->getCollection()->map(function($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->name,
                'phone' => $patient->phone,
                'email' => $patient->email,
                'gender_text' => $patient->gender_text,
                'photo_url' => $patient->photo ? asset('storage/' . $patient->photo) : null,
                'user_type_text' => $patient->user_type_text,
                'active_status_text' => $patient->active_status_text,
            ];
        });

        return response()->json([
            'data' => $data,
            'current_page' => $patients->currentPage(),
            'last_page' => $patients->lastPage(),
            'per_page' => $patients->perPage(),
            'total' => $patients->total(),
        ]);
    }
    public function searchNurses(Request $request)
    {
        $search = $request->get('search');
        $perPage = 10;

        $query = User::where('user_type', 'nurse')
                    ->where('activate', 1);

        // Search by name or phone
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $patients = $query->orderBy('name')
                        ->paginate($perPage);

        // Transform data for Select2
        $data = $patients->getCollection()->map(function($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->name,
                'phone' => $patient->phone,
                'email' => $patient->email,
                'gender_text' => $patient->gender_text,
                'photo_url' => $patient->photo ? asset('storage/' . $patient->photo) : null,
                'user_type_text' => $patient->user_type_text,
                'active_status_text' => $patient->active_status_text,
            ];
        });

        return response()->json([
            'data' => $data,
            'current_page' => $patients->currentPage(),
            'last_page' => $patients->lastPage(),
            'per_page' => $patients->perPage(),
            'total' => $patients->total(),
        ]);
    }

    public function searchDoctors(Request $request)
    {
        $search = $request->get('search');
        $perPage = 10;

        $query = User::where('user_type', 'doctor')
                    ->where('activate', 1);

        // Search by name or phone
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $patients = $query->orderBy('name')
                        ->paginate($perPage);

        // Transform data for Select2
        $data = $patients->getCollection()->map(function($patient) {
            return [
                'id' => $patient->id,
                'name' => $patient->name,
                'phone' => $patient->phone,
                'email' => $patient->email,
                'gender_text' => $patient->gender_text,
                'photo_url' => $patient->photo ? asset('assets/admin/uploads/' . $patient->photo) : null,
                'user_type_text' => $patient->user_type_text,
                'active_status_text' => $patient->active_status_text,
            ];
        });

        return response()->json([
            'data' => $data,
            'current_page' => $patients->currentPage(),
            'last_page' => $patients->lastPage(),
            'per_page' => $patients->perPage(),
            'total' => $patients->total(),
        ]);
    }

    /**
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 'patient');

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

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new patient
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created patient
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
            'fcm_token' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $userData = [
                'user_type' => 'patient',
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

            User::create($userData);

            return redirect()->route('users.index')
                ->with('success', __('messages.patient_created_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_creating_patient'))
                ->withInput();
        }
    }

    /**
     * Display the specified patient
     */
    public function show(User $user)
    {
        // Ensure we're only showing patients
        if ($user->user_type !== 'patient') {
            abort(404);
        }

        $user->load('families');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified patient
     */
    public function edit(User $user)
    {
        // Ensure we're only editing patients
        if ($user->user_type !== 'patient') {
            abort(404);
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, User $user)
    {
        // Ensure we're only updating patients
        if ($user->user_type !== 'patient') {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => [
                'required',
                'string',
                Rule::unique('users')->ignore($user->id)
            ],
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:1,2',
            'activate' => 'required|in:1,2',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'fcm_token' => 'nullable|string'
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

            $user->update($userData);

            return redirect()->route('users.index')
                ->with('success', __('messages.patient_updated_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_updating_patient'))
                ->withInput();
        }
    }

    /**
     * Remove the specified patient
     */
    public function destroy(User $user)
    {
        // Ensure we're only deleting patients
        if ($user->user_type !== 'patient') {
            abort(404);
        }

        try {
            // Delete photo if exists
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $user->delete();

            return redirect()->route('users.index')
                ->with('success', __('messages.patient_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_deleting_patient'));
        }
    }

    /**
     * Toggle user activation status
     */
    public function toggleStatus(User $user)
    {
        // Ensure we're only toggling patients
        if ($user->user_type !== 'patient') {
            abort(404);
        }

        try {
            $user->update([
                'activate' => $user->activate == 1 ? 2 : 1
            ]);

            $status = $user->activate == 1 ? __('messages.activated') : __('messages.deactivated');
            return redirect()->back()
                ->with('success', __('messages.patient_status_updated', ['status' => $status]));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_updating_status'));
        }
    }
}