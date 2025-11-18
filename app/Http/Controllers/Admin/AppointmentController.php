<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ElderlyCare;
use App\Models\HomeXray;
use App\Models\MedicalTest;
use App\Models\RequestNurse;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of all appointments.
     */
    public function index(Request $request)
    {
        $appointments = collect();
        
        // Get filter parameters
        $type = $request->get('type', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $userId = $request->get('user_id');
        $search = $request->get('search');
        
        // Base query conditions
        $conditions = function($query) use ($dateFrom, $dateTo, $userId, $search) {
            if ($dateFrom) {
                $query->where('date_of_appointment', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->where('date_of_appointment', '<=', $dateTo);
            }
            if ($userId) {
                $query->where('user_id', $userId);
            }
            if ($search) {
                $query->where('note', 'like', "%{$search}%");
            }
        };

        // Fetch data based on type filter
        if ($type === 'all' || $type === 'elderly_care') {
            $elderlyCares = ElderlyCare::with(['user', 'typeElderlyCare'])
                ->where($conditions)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'elderly_care',
                        'type_name' => $item->typeElderlyCare->type_of_service ?? 'N/A',
                        'service_name' => __('messages.' . ($item->typeElderlyCare->type_of_service ?? 'unknown')),
                        'price' => $item->typeElderlyCare->price ?? 0,
                        'date_of_appointment' => $item->date_of_appointment,
                        'time_of_appointment' => $item->time_of_appointment,
                        'note' => $item->note,
                        'user_name' => $item->user->name ?? 'N/A',
                        'user_email' => $item->user->email ?? 'N/A',
                        'created_at' => $item->created_at,
                        'model_instance' => $item
                    ];
                });
            $appointments = $appointments->merge($elderlyCares);
        }

        if ($type === 'all' || $type === 'home_xray') {
            $homeXrays = HomeXray::with(['user', 'typeHomeXray'])
                ->where($conditions)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'home_xray',
                        'type_name' => $item->typeHomeXray->name ?? 'N/A',
                        'service_name' => $item->typeHomeXray->name ?? 'N/A',
                        'price' => $item->typeHomeXray->price ?? 0,
                        'date_of_appointment' => $item->date_of_appointment,
                        'time_of_appointment' => $item->time_of_appointment,
                        'note' => $item->note,
                        'user_name' => $item->user->name ?? 'N/A',
                        'user_email' => $item->user->email ?? 'N/A',
                        'created_at' => $item->created_at,
                        'model_instance' => $item
                    ];
                });
            $appointments = $appointments->merge($homeXrays);
        }

        if ($type === 'all' || $type === 'medical_test') {
            $medicalTests = MedicalTest::with(['user', 'typeMedicalTest'])
                ->where($conditions)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'medical_test',
                        'type_name' => $item->typeMedicalTest->name ?? 'N/A',
                        'service_name' => $item->typeMedicalTest->name ?? 'N/A',
                        'price' => $item->typeMedicalTest->price ?? 0,
                        'date_of_appointment' => $item->date_of_appointment,
                        'time_of_appointment' => $item->time_of_appointment,
                        'note' => $item->note,
                        'user_name' => $item->user->name ?? 'N/A',
                        'user_email' => $item->user->email ?? 'N/A',
                        'created_at' => $item->created_at,
                        'model_instance' => $item
                    ];
                });
            $appointments = $appointments->merge($medicalTests);
        }

        // ADD THIS NEW SECTION FOR REQUEST NURSES
        if ($type === 'all' || $type === 'request_nurse') {
            $requestNurses = RequestNurse::with(['user', 'typeRequestNurse'])
                ->where($conditions)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'request_nurse',
                        'type_name' => $item->typeRequestNurse->type_of_service ?? 'N/A',
                        'service_name' => __('messages.' . ($item->typeRequestNurse->type_of_service ?? 'unknown')),
                        'price' => $item->typeRequestNurse->price ?? 0,
                        'date_of_appointment' => $item->date_of_appointment,
                        'time_of_appointment' => $item->time_of_appointment,
                        'note' => $item->note,
                        'user_name' => $item->user->name ?? 'N/A',
                        'user_email' => $item->user->email ?? 'N/A',
                        'created_at' => $item->created_at,
                        'model_instance' => $item
                    ];
                });
            $appointments = $appointments->merge($requestNurses);
        }

        // Sort by date and time
        // Sort by date and time (newest to oldest)
        $appointments = $appointments->sortBy([
            ['date_of_appointment', 'desc'],
            ['time_of_appointment', 'desc']
        ]);
        // Get users for filter dropdown
        $users = User::select('id', 'name')->orderBy('name')->get();

        // Manual pagination
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $total = $appointments->count();
        $appointments = $appointments->forPage($currentPage, $perPage);

        // Create pagination data
        $pagination = [
            'current_page' => $currentPage,
            'last_page' => ceil($total / $perPage),
            'per_page' => $perPage,
            'total' => $total,
            'from' => ($currentPage - 1) * $perPage + 1,
            'to' => min($currentPage * $perPage, $total),
        ];

        return view('admin.appointments.index', compact('appointments', 'users', 'pagination', 'type', 'dateFrom', 'dateTo', 'userId', 'search'));
    }
}