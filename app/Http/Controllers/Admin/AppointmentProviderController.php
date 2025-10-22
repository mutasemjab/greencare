<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppointmentProvider;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AppointmentProviderController extends Controller
{
    /**
     * Display a listing of appointment providers.
     */
    public function index(Request $request)
    {
        // Check permission
        if (!Gate::allows('appointmentProvider-table')) {
            abort(403, __('messages.unauthorized_access'));
        }

        $appointments = collect();
        
        // Get filter parameters
        $status = $request->get('status', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $userId = $request->get('user_id');
        $providerId = $request->get('provider_id');
        $search = $request->get('search');
        
        // Base query
        $query = AppointmentProvider::with(['user', 'provider']);

        // Apply filters
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($dateFrom) {
            $query->where('date_of_appointment', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('date_of_appointment', '<=', $dateTo);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($providerId) {
            $query->where('provider_id', $providerId);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name_of_patient', 'like', "%{$search}%")
                  ->orWhere('phone_of_patient', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Get appointments with pagination
        $appointments = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get users and providers for filter dropdowns
        $users = User::select('id', 'name')->orderBy('name')->get();
        $providers = Provider::select('id', 'name')->orderBy('name')->get();

        // Get status options
        $statusOptions = AppointmentProvider::getStatusOptions();

        return view('admin.appointment-providers.index', compact(
            'appointments', 
            'users', 
            'providers', 
            'statusOptions',
            'status', 
            'dateFrom', 
            'dateTo', 
            'userId', 
            'providerId',
            'search'
        ));
    }

    /**
     * Show the form for creating a new appointment provider.
     */
    public function create()
    {
        // Check permission
        if (!Gate::allows('appointmentProvider-add')) {
            abort(403, __('messages.unauthorized_access'));
        }

        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        $providers = Provider::select('id', 'name', 'price')->orderBy('name')->get();
        $statusOptions = AppointmentProvider::getStatusOptions();

        return view('admin.appointment-providers.create', compact('users', 'providers', 'statusOptions'));
    }

    /**
     * Store a newly created appointment provider in storage.
     */
    public function store(Request $request)
    {
        // Check permission
        if (!Gate::allows('appointmentProvider-add')) {
            abort(403, __('messages.unauthorized_access'));
        }

        $validator = Validator::make($request->all(), [
            'name_of_patient' => 'required|string|max:255',
            'phone_of_patient' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string|max:1000',
            'date_of_appointment' => 'nullable|date',
            'time_of_appointment' => 'nullable|date_format:H:i',
            'provider_id' => 'required|exists:providers,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:1,2,3,4,5'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            AppointmentProvider::create($request->all());

            return redirect()->route('admin.appointment-providers.index')
                ->with('success', __('messages.appointment_provider_created_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_creating_appointment_provider'))
                ->withInput();
        }
    }

    /**
     * Display the specified appointment provider.
     */
    public function show($id)
    {
        // Check permission
        if (!Gate::allows('appointmentProvider-table')) {
            abort(403, __('messages.unauthorized_access'));
        }

        $appointment = AppointmentProvider::with(['user', 'provider'])->findOrFail($id);

        return view('admin.appointment-providers.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment provider.
     */
    public function edit($id)
    {
        // Check permission
        if (!Gate::allows('appointmentProvider-edit')) {
            abort(403, __('messages.unauthorized_access'));
        }

        $appointment = AppointmentProvider::findOrFail($id);
        $users = User::select('id', 'name', 'email')->orderBy('name')->get();
        $providers = Provider::select('id', 'name', 'price')->orderBy('name')->get();
        $statusOptions = AppointmentProvider::getStatusOptions();

        return view('admin.appointment-providers.edit', compact('appointment', 'users', 'providers', 'statusOptions'));
    }

    /**
     * Update the specified appointment provider in storage.
     */
    public function update(Request $request, $id)
    {
        // Check permission
        if (!Gate::allows('appointmentProvider-edit')) {
            abort(403, __('messages.unauthorized_access'));
        }

        $appointment = AppointmentProvider::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_of_patient' => 'required|string|max:255',
            'phone_of_patient' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'lat' => 'nullable|numeric|between:-90,90',
            'lng' => 'nullable|numeric|between:-180,180',
            'description' => 'nullable|string|max:1000',
            'date_of_appointment' => 'nullable|date',
            'time_of_appointment' => 'nullable|date_format:H:i',
            'provider_id' => 'required|exists:providers,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:1,2,3,4,5'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $appointment->update($request->all());

            return redirect()->route('admin.appointment-providers.index')
                ->with('success', __('messages.appointment_provider_updated_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_updating_appointment_provider'))
                ->withInput();
        }
    }

    /**
     * Remove the specified appointment provider from storage.
     */
    public function destroy($id)
    {
        // Check permission
        if (!Gate::allows('appointmentProvider-delete')) {
            abort(403, __('messages.unauthorized_access'));
        }

        try {
            $appointment = AppointmentProvider::findOrFail($id);
            $appointment->delete();

            return redirect()->route('admin.appointment-providers.index')
                ->with('success', __('messages.appointment_provider_deleted_successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_deleting_appointment_provider'));
        }
    }

    /**
     * Update appointment status via AJAX
     */
    public function updateStatus(Request $request, $id)
    {
        // Check permission
        if (!Gate::allows('appointmentProvider-edit')) {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized_access')]);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:1,2,3,4,5'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => __('messages.validation_error')]);
        }

        try {
            $appointment = AppointmentProvider::findOrFail($id);
            $appointment->update(['status' => $request->status]);

            return response()->json([
                'success' => true, 
                'message' => __('messages.status_updated_successfully'),
                'status_name' => $appointment->status_name,
                'status_badge_class' => $appointment->status_badge_class
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => __('messages.error_updating_status')]);
        }
    }

  
}