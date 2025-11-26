<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\ReportTemplate;
use App\Models\Report;
use App\Models\ReportAnswer;
use App\Models\Medication;
use App\Models\MedicationSchedule;
use App\Models\User;
use App\Services\FirestoreRoomService;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RoomReportController extends Controller
{
    use Responses;

    public function getPatient()
    {
        $data = User::where('user_type', 'patient')->get();
        return $this->success_response('Patients retrieved successfully', $data);
    }


    public function getReportTemplates(Request $request)
    {
        try {
            $query = ReportTemplate::query();
            
            // Optional: Filter by user type if needed
            if ($request->has('created_for')) {
                $query->where('created_for', $request->created_for);
            }
            
            $templates = $query->get();
            
            return $this->success_response('Report templates retrieved successfully', [
                'templates' => $templates
            ]);
        } catch (\Exception $e) {
            return $this->error_response('Failed to retrieve templates', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Create a new room
     */
    public function createRoom(Request $request, FirestoreRoomService $firestoreService)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount' => 'required',
            'family_id' => 'nullable|exists:families,id',
            'patient_id' => 'required|exists:users,id',
            'report_templates' => 'nullable|array',
            'report_templates.*' => 'exists:report_templates,id',
            // Initial report validation rules
            'initial_report.template_id' => 'nullable|exists:report_templates,id',
            'initial_report.answers' => 'required_with:initial_report.template_id|array',
            'initial_report.answers.*.field_id' => 'required_with:initial_report.template_id|exists:report_fields,id',
            'initial_report.answers.*.value' => 'required_with:initial_report.template_id',
            'initial_report.medications' => 'nullable|array',
            'initial_report.medications.*.name' => 'required|string',
            'initial_report.medications.*.dosage' => 'nullable|string',
            'initial_report.medications.*.quantity' => 'nullable|integer',
            'initial_report.medications.*.notes' => 'nullable|string',
            'initial_report.medications.*.schedules' => 'required|array|min:1',
            'initial_report.medications.*.schedules.*.time' => 'required|date_format:H:i',
            'initial_report.medications.*.schedules.*.frequency' => 'required|in:daily,weekly,monthly',
        ]);

        if ($validator->fails()) {
            return $this->error_response('Validation failed', $validator->errors());
        }

        // Verify initial report template is initial setup type if provided
        if ($request->has('initial_report.template_id')) {
            $initialTemplate = ReportTemplate::find($request->input('initial_report.template_id'));
            if ($initialTemplate->report_type !== 'initial_setup') {
                return $this->error_response('Invalid template', 'Only initial setup templates are allowed');
            }
        }

        DB::beginTransaction();
        try {
            // Create the room
            $room = Room::create([
                'title' => $request->title,
                'description' => $request->description,
                'discount' => $request->discount,
                'family_id' => $request->family_id,
            ]);

            // Add patient to room
            $room->users()->attach($request->patient_id, ['role' => 'patient']);

            // Add current user (creator) to room
            $room->users()->attach(Auth::id(), ['role' => Auth::user()->user_type]);

            // Create reports from selected templates
            if ($request->has('report_templates')) {
                foreach ($request->report_templates as $templateId) {
                    Report::create([
                        'room_id' => $room->id,
                        'report_template_id' => $templateId,
                        'created_by' => Auth::id(),
                        'report_datetime' => now(),
                    ]);
                }
            }

            // Handle initial report submission if provided
            $initialReport = null;
            $medicationsCount = 0;
            
            if ($request->has('initial_report.template_id')) {
                // Create the initial report
                $initialReport = Report::create([
                    'room_id' => $room->id,
                    'report_template_id' => $request->input('initial_report.template_id'),
                    'created_by' => Auth::id(),
                ]);

                // Save report answers
                foreach ($request->input('initial_report.answers') as $index => $answer) {
                    // Get the field to check its input type
                    $field = \App\Models\ReportField::find($answer['field_id']);

                    $value = $answer['value'];

                    // Handle file uploads for photo, pdf, and signature fields
                    if ($field && in_array($field->input_type, ['photo', 'pdf', 'signuture'])) {
                        // Check if file exists in the request
                        $fileKey = "initial_report.answers.{$index}.value";
                        if ($request->hasFile($fileKey)) {
                            $uploadedFile = $request->file($fileKey);
                            $value = uploadImage('assets/admin/uploads', $uploadedFile);
                        }
                    }

                    ReportAnswer::create([
                        'report_id' => $initialReport->id,
                        'report_field_id' => $answer['field_id'],
                        'value' => json_encode($value),
                    ]);
                }

                // Get patient from room
                $patient = $room->users()->where('role', 'patient')->first();

                // Save medications if provided
                if ($request->has('initial_report.medications') && !empty($request->input('initial_report.medications'))) {
                    foreach ($request->input('initial_report.medications') as $medicationData) {
                        $medication = Medication::create([
                            'patient_id' => $patient->id,
                            'room_id' => $room->id,
                            'name' => $medicationData['name'],
                            'dosage' => $medicationData['dosage'] ?? null,
                            'quantity' => $medicationData['quantity'] ?? null,
                            'notes' => $medicationData['notes'] ?? null,
                        ]);

                        // Create medication schedules
                        foreach ($medicationData['schedules'] as $schedule) {
                            MedicationSchedule::create([
                                'medication_id' => $medication->id,
                                'time' => $schedule['time'],
                                'frequency' => $schedule['frequency'],
                            ]);
                        }
                    }
                    $medicationsCount = count($request->input('initial_report.medications'));
                }

                // Load the complete initial report with relationships
                $initialReport->load(['template.sections.fields.options', 'answers']);
            }

            // Sync to Firestore for chat functionality
            $firestoreService->syncRoom($room);

            DB::commit();

            $responseData = [
                'room' => $room->load('users', 'reports.reportTemplate')
            ];

            if ($initialReport) {
                $responseData['initial_report'] = $initialReport;
                $responseData['medications_count'] = $medicationsCount;
            }

            return $this->success_response('Room created successfully', $responseData);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error_response('Failed to create room', ['error' => $e->getMessage()]);
        }
    }


    /**
     * Get specific template with sections and fields (detail view)
     */
    public function getTemplateDetails(Request $request, $template_id)
    {
        $template = ReportTemplate::with(['sections.fields.options'])
            ->find($template_id);

        if (!$template) {
            return $this->error_response('Template not found', 404);
        }

        return $this->success_response('Template details retrieved successfully', [
            'template' => $template
        ]);
    }

    

    /**
     * Get available report templates for current user type
     */
    public function getAvailableTemplates(Request $request, $room_id)
    {

        // Verify user has access to the room
        $room = Room::find($room_id);
        $userInRoom = $room->users()->where('user_id', Auth::id())->first();

        // Get user type from users table directly
        $currentUser = Auth::user();
        $userType = $currentUser->user_type;

        // Map user_type to created_for field in templates
        $createdFor = ($userType === 'doctor') ? 'doctor' : 'nurse';

        // Get recurring templates for this user type
        $templates = ReportTemplate::where('report_type', 'recurring')
            ->where('created_for', $createdFor)
            ->with(['sections.fields.options'])
            ->first();

        return $this->success_response('Templates retrieved successfully', [
            'user_type' => $userType,
            'templates' => $templates
        ]);
    }



    /**
     * Submit a recurring report
     */

    public function submitRecurringReport(Request $request)
    {
        $currentUser = Auth::user();
        $userType = $currentUser->user_type;

        // Dynamic validation based on user type
        $rules = [
            'room_id' => 'required|exists:rooms,id',
            'template_id' => 'required|exists:report_templates,id',
            'answers' => 'required|array',
            'answers.*.field_id' => 'required|exists:report_fields,id',
            'answers.*.value' => 'required',
        ];

        // Add date/hour validation based on user type
        if ($userType === 'doctor') {
            $rules['date'] = 'required|date_format:Y-m-d'; // e.g., 2025-10-07
        } else if ($userType === 'nurse') {
            $rules['hour'] = 'required|date_format:H:i'; // e.g., 04:00
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->error_response('Validation failed', $validator->errors());
        }

        // Verify user has access to the room
        $room = Room::find($request->room_id);
        $userInRoom = $room->users()->where('user_id', Auth::id())->first();



        // Verify template is recurring type and matches user type
        $template = ReportTemplate::find($request->template_id);
        if ($template->report_type !== 'recurring') {
            return $this->error_response('Invalid template', 'Only recurring templates are allowed');
        }

        // Map user_type to created_for field in templates
        $createdFor = ($userType === 'doctor') ? 'doctor' : 'nurse';


        DB::beginTransaction();
        try {
            // Prepare report datetime based on user type
            $reportDatetime = null;

            if ($userType === 'doctor') {
                $reportDatetime = $request->date . ' ' . now()->format('H:i:s');

                // 🔥 Prevent duplicate report in the same hour (doctor)
                $existingReport = Report::where('room_id', $request->room_id)
                    ->whereDate('report_datetime', $request->date)
                    ->exists();

                if ($existingReport) {
                    return $this->error_response('A report for this hour already exists for doctor', null);
                }
            } else if ($userType === 'nurse') {
                $reportDatetime = now()->format('Y-m-d') . ' ' . $request->hour . ':00';

                // 🔥 Prevent duplicate reports in the same hour
                $existingReport = Report::where('room_id', $request->room_id)
                    ->whereDate('report_datetime', now()->format('Y-m-d'))
                    ->whereRaw('HOUR(report_datetime) = ?', [date('H', strtotime($reportDatetime))])
                    ->exists();

                if ($existingReport) {
                    return $this->error_response('A report for this hour already exists', null);
                }
            }

            // Create the report
            $report = Report::create([
                'room_id' => $request->room_id,
                'report_template_id' => $request->template_id,
                'created_by' => Auth::id(),
                'report_datetime' => $reportDatetime,
            ]);

            // Save report answers
            foreach ($request->answers as $answer) {
                ReportAnswer::create([
                    'report_id' => $report->id,
                    'report_field_id' => $answer['field_id'],
                    'value' => json_encode($answer['value']),
                ]);
            }

            DB::commit();

            // Load the complete report with relationships
            $report->load(['template.sections.fields.options', 'answers']);

            return $this->success_response('Report submitted successfully', [
                'report' => $report
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error_response('Failed to submit report', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get reports by date (for doctors) or date+hour (for nurses)
     */
    public function getReportsByTime(Request $request)
    {
        $currentUser = Auth::user();
        $userType = $currentUser->user_type;

        // Validate request - same validation for all user types now
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date_format:Y-m-d',
            'hour' => 'nullable|date_format:H', // Optional - if provided, filter by hour
            'report_type' => 'nullable|in:doctor,nurse,all', // Optional - filter by report creator type
        ]);

        if ($validator->fails()) {
            return $this->error_response('Validation failed', $validator->errors());
        }

        // Verify user has access to the room
        $room = Room::find($request->room_id);
        

        // Build query - accessible to all user types (patient, doctor, nurse)
        $query = Report::with(['template.sections.fields.options', 'answers', 'creator'])
            ->where('room_id', $request->room_id)
            ->whereNotNull('report_datetime');

        // Filter by date
        $query->whereDate('report_datetime', $request->date);

        // Optionally filter by hour if provided
        if ($request->filled('hour')) {
            $query->whereRaw('HOUR(report_datetime) = ?', [$request->hour]);
        }

        // Optionally filter by report creator type (doctor or nurse reports)
        if ($request->filled('report_type') && $request->report_type !== 'all') {
            $query->whereHas('creator', function ($q) use ($request) {
                $q->where('user_type', $request->report_type);
            });
        }

        $reports = $query->orderBy('report_datetime', 'desc')->get();

        // Merge answers into fields
        $reports->each(function ($report) {
            $answersGrouped = $report->answers->keyBy('report_field_id');

            $report->template->sections->each(function ($section) use ($answersGrouped) {
                $section->fields->each(function ($field) use ($answersGrouped) {
                    $field->answer = $answersGrouped[$field->id]->value ?? null;
                });
            });

            unset($report->answers);
        });

        return $this->success_response('Reports retrieved successfully', [
            'reports' => $reports,
            'count' => $reports->count(),
            'filters_applied' => [
                'date' => $request->date,
                'hour' => $request->hour ?? 'all hours',
                'report_type' => $request->report_type ?? 'all types'
            ]
        ]);
    }

    /**
     * Get room reports history
     */
    public function getRoomReports(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'template_type' => 'nullable|in:initial_setup,recurring',
        ]);

        if ($validator->fails()) {
            return $this->error_response('Validation failed', $validator->errors());
        }

        // Detect language from header (default en)
        $lang = $request->header('Accept-Language', 'en');
        $lang = in_array(strtolower($lang), ['ar', 'en']) ? strtolower($lang) : 'en';

        $room = Room::find($request->room_id);


        // Fetch reports with relations
        $query = Report::where('room_id', $request->room_id)
            ->with([
                'template.sections.fields.options',
                'answers.field.section'
            ]);

        if ($request->has('template_type')) {
            $query->whereHas('template', function ($q) use ($request) {
                $q->where('report_type', $request->template_type);
            });
        }

        $reports = $query->latest()->get();

        // Build templates → sections → fields (with answers)
        $templates = [];

        foreach ($reports as $report) {
            $template = $report->template;

            // Skip if already added (to merge multiple reports of same template)
            if (!isset($templates[$template->id])) {
                $templates[$template->id] = [
                    'id' => $template->id,
                    'title' => $template->{'title_' . $lang},
                    'report_type' => $template->report_type,
                    'sections' => [],
                ];
            }

            // Map sections
            foreach ($template->sections as $section) {
                $sectionData = [
                    'id' => $section->id,
                    'title' => $section->{'title_' . $lang},
                    'fields' => [],
                ];

                foreach ($section->fields as $field) {
                    // Find the answer for this field in the current report
                    $answer = $report->answers->firstWhere('report_field_id', $field->id);

                    $sectionData['fields'][] = [
                        'id' => $field->id,
                        'label' => $field->{'label_' . $lang},
                        'input_type' => $field->input_type,
                        'answer' => $answer ? json_decode($answer->value, true) : null,
                    ];
                }

                $templates[$template->id]['sections'][] = $sectionData;
            }
        }

        // Re-index array
        $templates = array_values($templates);

        return $this->success_response('Reports retrieved successfully', [
            'room' => $room,
            'templates' => $templates,
        ]);
    }



    /**
     * Get specific report details
     */
    public function getReport($reportId)
    {
        $report = Report::with([
            'template.sections.fields.options',
            'answers.field',
            'creator:id,name',
            'room:id,title,description'
        ])->find($reportId);

        if (!$report) {
            return $this->error_response('Report not found', null);
        }



        return $this->success_response('Report retrieved successfully', [
            'report' => $report
        ]);
    }

    /**
     * Get room medications
     */
    public function getRoomMedications(Request $request, $room_id)
    {

        // Verify user has access to the room
        $room = Room::find($room_id);


        $medications = Medication::where('room_id', $request->room_id)
            ->with(['schedules', 'patient:id,name'])
            ->get();

        return $this->success_response('Medications retrieved successfully', [
            'medications' => $medications
        ]);
    }


    /**
     * Get all rooms for the authenticated nurse with completion status
     */
   public function getNurseRooms(Request $request)
    {
        $currentUser = Auth::user();
        $userType = $currentUser->user_type;

        // Get all rooms where the nurse is a member
        $rooms = Room::whereHas('users', function ($query) use ($currentUser) {
            $query->where('user_id', $currentUser->id)
                ->where('role', 'nurse');
        })
            ->with(['users']) // Load room users if needed
            ->get();

        // Add is_complete flag to each room
        $roomsWithStatus = $rooms->map(function ($room) {
            // Check if there are any pledge forms for this room
            $hasPledgeForm = \App\Models\PledgeForm::where('room_id', $room->id)->exists();

            return [
                'id' => $room->id,
                'title' => $room->title,
                'description' => $room->description,
                'discount' => $room->discount,
                'family_id' => $room->family_id,
                'created_at' => $room->created_at,
                'updated_at' => $room->updated_at,
                'is_complete' => $hasPledgeForm,
            ];
        });

        return $this->success_response('Rooms retrieved successfully', [
            'rooms' => $roomsWithStatus,
            'count' => $roomsWithStatus->count()
        ]);
    }
}
