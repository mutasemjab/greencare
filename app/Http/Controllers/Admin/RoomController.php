<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Room;
use App\Models\RoomUser;
use App\Models\Family;
use App\Models\FamilyUser;
use App\Models\Medication;
use App\Models\User;
use App\Models\ReportTemplate;
use App\Models\Report;
use App\Services\FirestoreRoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
     protected $firestoreService;

    public function __construct(FirestoreRoomService $firestoreService)
    {
        $this->middleware('permission:room-table', ['only' => ['index', 'show']]);
        $this->middleware('permission:room-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:room-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:room-delete', ['only' => ['destroy']]);

         $this->firestoreService = $firestoreService;
    }

    /**
     * Display a listing of rooms
     */
    public function index(Request $request)
    {
        $query = Room::with(['family', 'patients', 'doctors', 'nurses']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('family', function($fq) use ($search) {
                      $fq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by family
        if ($request->has('family_id') && !empty($request->family_id)) {
            $query->where('family_id', $request->family_id);
        }

        $rooms = $query->orderBy('created_at', 'desc')->paginate(15);
        $families = Family::orderBy('name')->get();

        return view('admin.rooms.index', compact('rooms', 'families'));
    }

    /**
     * Show the form for creating a new room
     */
    public function create()
    {
        $families = Family::orderBy('name')->get();
        return view('admin.rooms.create', compact('families'));
    }

   
    /**
     * Store a newly created room
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount' => 'required',
            'family_id' => 'nullable|exists:families,id',
            'patients' => 'required|array|min:1',
            'patients.*' => 'exists:users,id',
            'doctors' => 'nullable|array',
            'doctors.*' => 'exists:users,id',
            'nurses' => 'nullable|array',
            'nurses.*' => 'exists:users,id',
            // Reports validation
            'report_templates' => 'nullable|array',
            'report_templates.*' => 'exists:report_templates,id',
            // Medications validation
            'medications' => 'nullable|array',
            'medications.*.patient_id' => 'required|exists:users,id',
            'medications.*.name' => 'required|string|max:255',
            'medications.*.dosage' => 'nullable|string|max:100',
            'medications.*.quantity' => 'nullable|integer|min:1',
            'medications.*.notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
       // try {
            // Create room
            $room = Room::create([
                'title' => $request->title,
                'description' => $request->description,
                'discount' => $request->discount,
                'family_id' => $request->family_id,
            ]);

            // Add patients
            foreach ($request->patients as $patientId) {
                RoomUser::create([
                    'room_id' => $room->id,
                    'user_id' => $patientId,
                    'role' => 'patient'
                ]);
            }

            // Add doctors
            if ($request->has('doctors')) {
                foreach ($request->doctors as $doctorId) {
                    RoomUser::create([
                        'room_id' => $room->id,
                        'user_id' => $doctorId,
                        'role' => 'doctor'
                    ]);
                }
            }

            // Add nurses
            if ($request->has('nurses')) {
                foreach ($request->nurses as $nurseId) {
                    RoomUser::create([
                        'room_id' => $room->id,
                        'user_id' => $nurseId,
                        'role' => 'nurse'
                    ]);
                }
            }

            // Create reports from selected templates
            if ($request->has('report_templates')) {
                foreach ($request->report_templates as $templateId) {
                    Report::create([
                        'room_id' => $room->id,
                        'report_template_id' => $templateId,
                        'created_by' => auth()->id(), // Current user (doctor/nurse)
                    ]);
                }
            }

            // Create medications
            if ($request->has('medications')) {
                foreach ($request->medications as $medicationData) {
                    // Only create if required fields are present
                    if (!empty($medicationData['patient_id']) && !empty($medicationData['name'])) {
                        Medication::create([
                            'patient_id' => $medicationData['patient_id'],
                            'room_id' => $room->id,
                            'name' => $medicationData['name'],
                            'dosage' => $medicationData['dosage'] ?? null,
                            'quantity' => $medicationData['quantity'] ?? null,
                            'notes' => $medicationData['notes'] ?? null,
                        ]);
                    }
                }
            }

            // Sync to Firestore for chat functionality
            $this->firestoreService->syncRoom($room);

            DB::commit();
            return redirect()->route('rooms.index')
                ->with('success', __('messages.room_created_successfully'));

        // } catch (\Exception $e) {
        //     DB::rollback();
        //     return redirect()->back()
        //         ->with('error', __('messages.error_creating_room'))
        //         ->withInput();
        // }
    }

    /**
     * Display the specified room
     */
    public function show(Room $room)
    {
        $room->load([
            'family',
            'patients',
            'doctors', 
            'nurses',
            'familyMembers',
            'reports.template',
            'reports.creator',
            'medications.patient',
            'medications.schedules'
        ]);

        $reportTemplates = ReportTemplate::orderBy('title_en')->get();
        
        return view('admin.rooms.show', compact('room', 'reportTemplates'));
    }

    /**
     * Show the form for editing the specified room
     */
    public function edit(Room $room)
    {
        $room->load(['patients', 'doctors', 'nurses', 'familyMembers']);
        $families = Family::orderBy('name')->get();
        
        return view('admin.rooms.edit', compact('room', 'families'));
    }

    /**
     * Update the specified room
     */
    public function update(Request $request, Room $room)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount' => 'nullable',
            'family_id' => 'nullable|exists:families,id',
            'patients' => 'required|array|min:1',
            'patients.*' => 'exists:users,id',
            'doctors' => 'nullable|array',
            'doctors.*' => 'exists:users,id',
            'nurses' => 'nullable|array',
            'nurses.*' => 'exists:users,id',
            'family_members' => 'nullable|array',
            'family_members.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Update room basic info
            $room->update([
                'title' => $request->title,
                'description' => $request->description,
                'discount' => $request->discount,
                'family_id' => $request->family_id,
            ]);

            // Remove existing room users
            RoomUser::where('room_id', $room->id)->delete();

            // Add patients
            foreach ($request->patients as $patientId) {
                RoomUser::create([
                    'room_id' => $room->id,
                    'user_id' => $patientId,
                    'role' => 'patient'
                ]);
            }

            // Add doctors
            if ($request->has('doctors')) {
                foreach ($request->doctors as $doctorId) {
                    RoomUser::create([
                        'room_id' => $room->id,
                        'user_id' => $doctorId,
                        'role' => 'doctor'
                    ]);
                }
            }

            // Add nurses
            if ($request->has('nurses')) {
                foreach ($request->nurses as $nurseId) {
                    RoomUser::create([
                        'room_id' => $room->id,
                        'user_id' => $nurseId,
                        'role' => 'nurse'
                    ]);
                }
            }

            // Add family members
            if ($request->has('family_members')) {
                foreach ($request->family_members as $familyMemberId) {
                    RoomUser::create([
                        'room_id' => $room->id,
                        'user_id' => $familyMemberId,
                        'role' => 'family'
                    ]);
                }
            }

            // Sync updated room to Firestore
            $this->firestoreService->syncRoom($room);

            DB::commit();
            return redirect()->route('rooms.index')
                ->with('success', __('messages.room_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', __('messages.error_updating_room'))
                ->withInput();
        }
    }

    /**
     * Remove the specified room
     */
    public function destroy(Room $room)
    {
        try {
            // Delete from Firestore first
            $this->firestoreService->deleteRoom($room->id);
            
            // Then delete from database
            $room->delete();
            
            return redirect()->route('rooms.index')
                ->with('success', __('messages.room_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_deleting_room'));
        }
    }

    /**
     * Add user to room
     */
    public function addUser(Request $request, Room $room)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:patient,family,doctor,nurse'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            // Check if user is already in room
            $exists = RoomUser::where('room_id', $room->id)
                ->where('user_id', $request->user_id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->with('error', __('messages.user_already_in_room'));
            }

            RoomUser::create([
                'room_id' => $room->id,
                'user_id' => $request->user_id,
                'role' => $request->role
            ]);

            // Sync to Firestore
            $this->firestoreService->addUserToRoom($room);

            return redirect()->back()
                ->with('success', __('messages.user_added_to_room_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_adding_user_to_room'));
        }
    }

    /**
     * Remove user from room
     */
    public function removeUser(Request $request, Room $room)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            RoomUser::where('room_id', $room->id)
                ->where('user_id', $request->user_id)
                ->delete();

            // Sync to Firestore
            $this->firestoreService->removeUserFromRoom($room);

            return redirect()->back()
                ->with('success', __('messages.user_removed_from_room_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_removing_user_from_room'));
        }
    }


    /**
     * Get rooms for AJAX calls
     */
    public function getRooms(Request $request)
    {
        $query = Room::with('family');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('family_id')) {
            $query->where('family_id', $request->family_id);
        }

        $rooms = $query->select('id', 'title', 'family_id')
                      ->orderBy('title')
                      ->get();

        return response()->json($rooms);
    }

    /**
     * Get room statistics
     */
    public function getStats(Room $room)
    {
        $stats = [
            'users' => [
                'patients' => $room->patients()->count(),
                'doctors' => $room->doctors()->count(), 
                'nurses' => $room->nurses()->count(),
                'family' => $room->familyMembers()->count(),
                'total' => $room->users()->count()
            ],
            'reports' => [
                'total' => $room->reports()->count(),
                'recent' => $room->reports()->where('created_at', '>=', now()->subDays(7))->count()
            ],
            'medications' => [
                'total' => $room->medications()->count(),
                'active' => $room->activeMedications()->count()
            ]
        ];

        return response()->json($stats);
    }
}