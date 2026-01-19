<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransferPatient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferPatientController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:transferPatient-table', ['only' => ['index', 'show']]);
        $this->middleware('permission:transferPatient-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:transferPatient-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:transferPatient-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TransferPatient::with('user');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('date_of_transfer', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date_of_transfer', '<=', $request->date_to);
        }

        // Filter by place type
        if ($request->filled('from_place')) {
            $query->where('from_place', $request->from_place);
        }

        if ($request->filled('to_place')) {
            $query->where('to_place', $request->to_place);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('from_address', 'LIKE', "%{$search}%")
                  ->orWhere('to_address', 'LIKE', "%{$search}%")
                  ->orWhere('note', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                               ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $transfers = $query->latest('date_of_transfer')
            ->latest('time_of_transfer')
            ->paginate(15);

        $users = User::orderBy('name')->get();

        return view('admin.transfer-patients.index', compact('transfers', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('admin.transfer-patients.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date_of_transfer' => 'required|date|after_or_equal:today',
            'time_of_transfer' => 'nullable|date_format:H:i',
            'note' => 'nullable|string|max:1000',
            'from_address' => 'required|string|max:500',
            'from_lat' => 'nullable|numeric|between:-90,90',
            'from_lng' => 'nullable|numeric|between:-180,180',
            'from_place' => 'required|integer|in:1,2',
            'to_address' => 'required|string|max:500',
            'to_lat' => 'nullable|numeric|between:-90,90',
            'to_lng' => 'nullable|numeric|between:-180,180',
            'to_place' => 'required|integer|in:1,2',
        ]);

        try {
            DB::beginTransaction();

            $transfer = TransferPatient::create($validated);

            DB::commit();

            return redirect()->route('transfer-patients.index')
                ->with('success', __('messages.transfer_created_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', __('messages.error_creating_transfer') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TransferPatient $transferPatient)
    {
        $transferPatient->load('user');
        return view('admin.transfer-patients.show', compact('transferPatient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransferPatient $transferPatient)
    {
        $users = User::orderBy('name')->get();
        return view('admin.transfer-patients.edit', compact('transferPatient', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransferPatient $transferPatient)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date_of_transfer' => 'required|date',
            'time_of_transfer' => 'nullable|date_format:H:i',
            'note' => 'nullable|string|max:1000',
            'from_address' => 'required|string|max:500',
            'from_lat' => 'nullable|numeric|between:-90,90',
            'from_lng' => 'nullable|numeric|between:-180,180',
            'from_place' => 'required|integer|in:1,2',
            'to_address' => 'required|string|max:500',
            'to_lat' => 'nullable|numeric|between:-90,90',
            'to_lng' => 'nullable|numeric|between:-180,180',
            'to_place' => 'required|integer|in:1,2',
        ]);

        try {
            DB::beginTransaction();

            $transferPatient->update($validated);

            DB::commit();

            return redirect()->route('transfer-patients.index')
                ->with('success', __('messages.transfer_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', __('messages.error_updating_transfer') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransferPatient $transferPatient)
    {
        try {
            $transferPatient->delete();

            return redirect()->route('transfer-patients.index')
                ->with('success', __('messages.transfer_deleted_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_deleting_transfer') . ': ' . $e->getMessage());
        }
    }
}