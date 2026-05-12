<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\MedicalTest;
use App\Models\AppointmentResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $lab = auth('lab')->user();

        $status   = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $search   = $request->get('search');

        $appointments = MedicalTest::with(['user', 'typeMedicalTest', 'room', 'result'])
            ->where('lab_id', $lab->id)
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($dateFrom, fn($q) => $q->whereDate('date_of_appointment', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('date_of_appointment', '<=', $dateTo))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%'));
            })
            ->orderBy('date_of_appointment', 'desc')
            ->orderBy('time_of_appointment', 'desc')
            ->get()
            ->map(function ($item) {
                $item->appointment_type = 'medical_test';
                return $item;
            });

        return view('lab.appointments.index', compact('lab', 'appointments', 'status', 'dateFrom', 'dateTo', 'search'));
    }

    public function show($type, $id)
    {
        $lab = auth('lab')->user();

        if ($type !== 'medical_test') {
            abort(404);
        }

        $appointment = MedicalTest::with(['user', 'typeMedicalTest', 'room', 'result.lab'])
            ->where('lab_id', $lab->id)
            ->findOrFail($id);

        $appointment->appointment_type = 'medical_test';

        return view('lab.appointments.show', compact('lab', 'appointment', 'type'));
    }

    public function updateStatus(Request $request, $type, $id)
    {
        if ($type !== 'medical_test') {
            return redirect()->route('lab.appointments.index')->with('error', 'نوع الموعد غير صحيح');
        }

        $lab = auth('lab')->user();

        $validator = Validator::make($request->all(), [
            'status'              => 'required|in:confirmed,processing,finished,cancelled',
            'cancellation_reason' => 'nullable|required_if:status,cancelled|string|max:500',
        ], [
            'status.required'                 => 'الحالة مطلوبة',
            'status.in'                       => 'الحالة غير صحيحة',
            'cancellation_reason.required_if' => 'سبب الإلغاء مطلوب عند إلغاء الموعد',
        ]);

        if ($validator->fails()) {
            return redirect()->route('lab.appointments.show', ['type' => $type, 'id' => $id])
                ->withErrors($validator)
                ->withInput();
        }

        $appointment = MedicalTest::where('lab_id', $lab->id)->findOrFail($id);

        $updateData = ['status' => $request->status];

        if ($request->status === 'cancelled' && $request->cancellation_reason) {
            $updateData['note'] = ($appointment->note ? $appointment->note . "\n\n" : '') .
                'سبب الإلغاء: ' . $request->cancellation_reason;
        }

        $appointment->update($updateData);

        return redirect()->route('lab.appointments.show', ['type' => $type, 'id' => $id])
            ->with('success', 'تم تحديث حالة الموعد بنجاح');
    }

    public function uploadResults(Request $request, $type, $id)
    {
        if ($type !== 'medical_test') {
            return redirect()->route('lab.appointments.show', ['type' => $type, 'id' => $id])
                ->with('error', 'نوع الموعد غير صحيح');
        }

        $lab = auth('lab')->user();

        $validator = Validator::make($request->all(), [
            'notes'    => 'nullable|string|max:2000',
            'files'    => 'required|array|min:1|max:10',
            'files.*'  => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ], [
            'notes.max'      => 'الملاحظات يجب ألا تتجاوز 2000 حرف',
            'files.required' => 'يجب رفع ملف واحد على الأقل',
            'files.min'      => 'يجب رفع ملف واحد على الأقل',
            'files.max'      => 'الحد الأقصى 10 ملفات',
            'files.*.mimes'  => 'يجب أن تكون الملفات من نوع: PDF, JPG, JPEG, PNG',
            'files.*.max'    => 'حجم الملف يجب ألا يتجاوز 10 ميجا',
        ]);

        if ($validator->fails()) {
            return redirect()->route('lab.appointments.show', ['type' => $type, 'id' => $id])
                ->withErrors($validator)
                ->withInput();
        }

        $appointment = MedicalTest::where('lab_id', $lab->id)->findOrFail($id);

        $uploadedFiles = [];
        foreach ($request->file('files') as $file) {
            $uploadedFiles[] = uploadImage('assets/admin/uploads/results', $file);
        }

        $result = $appointment->result;

        if ($result) {
            $result->update([
                'files'        => array_merge($result->files ?? [], $uploadedFiles),
                'notes'        => $request->notes ?? $result->notes,
                'completed_at' => now(),
            ]);
        } else {
            AppointmentResult::create([
                'appointment_type' => MedicalTest::class,
                'appointment_id'   => $appointment->id,
                'lab_id'           => $lab->id,
                'files'            => $uploadedFiles,
                'notes'            => $request->notes,
                'completed_at'     => now(),
            ]);
        }

        $appointment->update(['status' => 'finished']);

        return redirect()->route('lab.appointments.show', ['type' => $type, 'id' => $id])
            ->with('success', 'تم رفع النتائج بنجاح');
    }

    public function deleteFile(Request $request, $resultId)
    {
        $lab = auth('lab')->user();

        $validator = Validator::make($request->all(), [
            'file_path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'مسار الملف مطلوب'], 422);
        }

        $result = AppointmentResult::where('lab_id', $lab->id)->findOrFail($resultId);

        $files        = $result->files ?? [];
        $fileToDelete = $request->file_path;

        if (($key = array_search($fileToDelete, $files)) !== false) {
            unset($files[$key]);
            $result->update(['files' => array_values($files)]);

            $fullPath = public_path('assets/admin/uploads/results/' . $fileToDelete);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            return response()->json(['success' => true, 'message' => 'تم حذف الملف بنجاح']);
        }

        return response()->json(['success' => false, 'message' => 'الملف غير موجود'], 404);
    }
}
