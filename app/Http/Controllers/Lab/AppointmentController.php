<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\MedicalTest;
use App\Models\HomeXray;
use App\Models\AppointmentResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppointmentController extends Controller
{
    /**
     * عرض جميع المواعيد
     */
    public function index(Request $request)
    {
        $lab = auth('lab')->user();
        
        $type = $request->get('type', 'all'); // all, medical_test, home_xray
        $status = $request->get('status'); // pending, confirmed, processing, finished, cancelled
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');
        
        $appointments = collect();
        
        // جلب الفحوصات الطبية
        if ($type === 'all' || $type === 'medical_test') {
            $medicalTests = MedicalTest::with(['user', 'typeMedicalTest', 'room', 'result'])
                ->where('lab_id', $lab->id)
                ->when($status, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->when($dateFrom, function ($query) use ($dateFrom) {
                    return $query->whereDate('date_of_appointment', '>=', $dateFrom);
                })
                ->when($dateTo, function ($query) use ($dateTo) {
                    return $query->whereDate('date_of_appointment', '<=', $dateTo);
                })
                ->when($search, function ($query) use ($search) {
                    return $query->whereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                          ->orWhere('phone', 'like', '%' . $search . '%');
                    });
                })
                ->orderBy('date_of_appointment', 'desc')
                ->orderBy('time_of_appointment', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->appointment_type = 'medical_test';
                    return $item;
                });
                
            $appointments = $appointments->merge($medicalTests);
        }
        
        // جلب الأشعة المنزلية
        if ($type === 'all' || $type === 'home_xray') {
            $homeXrays = HomeXray::with(['user', 'typeHomeXray.parent', 'room', 'result'])
                ->where('lab_id', $lab->id)
                ->when($status, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->when($dateFrom, function ($query) use ($dateFrom) {
                    return $query->whereDate('date_of_appointment', '>=', $dateFrom);
                })
                ->when($dateTo, function ($query) use ($dateTo) {
                    return $query->whereDate('date_of_appointment', '<=', $dateTo);
                })
                ->when($search, function ($query) use ($search) {
                    return $query->whereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                          ->orWhere('phone', 'like', '%' . $search . '%');
                    });
                })
                ->orderBy('date_of_appointment', 'desc')
                ->orderBy('time_of_appointment', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->appointment_type = 'home_xray';
                    return $item;
                });
                
            $appointments = $appointments->merge($homeXrays);
        }
        
        // ترتيب حسب التاريخ
        if ($type === 'all') {
            $appointments = $appointments->sortByDesc('date_of_appointment')->values();
        }
        
        return view('lab.appointments.index', compact('lab', 'appointments', 'type', 'status', 'dateFrom', 'dateTo', 'search'));
    }
    
    /**
     * عرض تفاصيل الموعد
     */
    public function show($type, $id)
    {
        $lab = auth('lab')->user();
        
        if (!in_array($type, ['medical_test', 'home_xray'])) {
            abort(404);
        }
        
        if ($type === 'medical_test') {
            $appointment = MedicalTest::with(['user', 'typeMedicalTest', 'room', 'result.lab'])
                ->where('lab_id', $lab->id)
                ->findOrFail($id);
        } else {
            $appointment = HomeXray::with(['user', 'typeHomeXray.parent', 'room', 'result.lab'])
                ->where('lab_id', $lab->id)
                ->findOrFail($id);
        }
        
        $appointment->appointment_type = $type;
        
        return view('lab.appointments.show', compact('lab', 'appointment', 'type'));
    }
    
    /**
     * تحديث حالة الموعد
     */
    public function updateStatus(Request $request, $type, $id)
    {
        $lab = auth('lab')->user();
        
        $request->validate([
            'status' => 'required|in:confirmed,processing,finished,cancelled',
            'cancellation_reason' => 'required_if:status,cancelled|string|max:500',
        ], [
            'status.required' => 'الحالة مطلوبة',
            'status.in' => 'الحالة غير صحيحة',
            'cancellation_reason.required_if' => 'سبب الإلغاء مطلوب عند إلغاء الموعد',
        ]);
        
        if (!in_array($type, ['medical_test', 'home_xray'])) {
            return back()->with('error', 'نوع الموعد غير صحيح');
        }
        
        if ($type === 'medical_test') {
            $appointment = MedicalTest::where('lab_id', $lab->id)->findOrFail($id);
        } else {
            $appointment = HomeXray::where('lab_id', $lab->id)->findOrFail($id);
        }
        
        $updateData = ['status' => $request->status];
        
        if ($request->status === 'cancelled' && $request->cancellation_reason) {
            $updateData['note'] = ($appointment->note ? $appointment->note . "\n\n" : '') .
                "سبب الإلغاء: " . $request->cancellation_reason;
        }
        
        $appointment->update($updateData);
        
        return back()->with('success', 'تم تحديث حالة الموعد بنجاح');
    }
    
    /**
     * رفع النتائج
     */
    public function uploadResults(Request $request, $type, $id)
    {
        $lab = auth('lab')->user();
        
        $request->validate([
            'notes' => 'nullable|string|max:2000',
            'files' => 'required|array|min:1|max:10',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB
        ], [
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 2000 حرف',
            'files.required' => 'يجب رفع ملف واحد على الأقل',
            'files.array' => 'يجب أن تكون الملفات في صيغة صحيحة',
            'files.min' => 'يجب رفع ملف واحد على الأقل',
            'files.max' => 'الحد الأقصى 10 ملفات',
            'files.*.mimes' => 'يجب أن تكون الملفات من نوع: PDF, JPG, JPEG, PNG',
            'files.*.max' => 'حجم الملف يجب ألا يتجاوز 10 ميجا',
        ]);
        
        if (!in_array($type, ['medical_test', 'home_xray'])) {
            return back()->with('error', 'نوع الموعد غير صحيح');
        }
        
        if ($type === 'medical_test') {
            $appointment = MedicalTest::where('lab_id', $lab->id)->findOrFail($id);
            $appointmentType = MedicalTest::class;
        } else {
            $appointment = HomeXray::where('lab_id', $lab->id)->findOrFail($id);
            $appointmentType = HomeXray::class;
        }
        
        // رفع الملفات
        $uploadedFiles = [];
        foreach ($request->file('files') as $file) {
            $path = uploadImage('assets/admin/uploads/results', $file);
            $uploadedFiles[] = $path;
        }
        
        // إنشاء أو تحديث النتيجة
        $result = $appointment->result;
        
        if ($result) {
            // إضافة الملفات الجديدة للملفات الموجودة
            $existingFiles = $result->files ?? [];
            $result->update([
                'files' => array_merge($existingFiles, $uploadedFiles),
                'notes' => $request->notes ?? $result->notes,
                'completed_at' => now(),
            ]);
        } else {
            $result = AppointmentResult::create([
                'appointment_type' => $appointmentType,
                'appointment_id' => $appointment->id,
                'lab_id' => $lab->id,
                'files' => $uploadedFiles,
                'notes' => $request->notes,
                'completed_at' => now(),
            ]);
        }
        
        // تحديث حالة الموعد إلى منتهي
        $appointment->update(['status' => 'finished']);
        
        return back()->with('success', 'تم رفع النتائج بنجاح');
    }
    
    /**
     * حذف ملف من النتائج
     */
    public function deleteFile(Request $request, $resultId)
    {
        $lab = auth('lab')->user();
        
        $request->validate([
            'file_path' => 'required|string',
        ]);
        
        $result = AppointmentResult::where('lab_id', $lab->id)->findOrFail($resultId);
        
        $files = $result->files ?? [];
        $fileToDelete = $request->file_path;
        
        // إزالة الملف من القاعدة
        if (($key = array_search($fileToDelete, $files)) !== false) {
            unset($files[$key]);
            $result->update(['files' => array_values($files)]);
            
            // حذف الملف من السيرفر
            $fullPath = public_path('assets/admin/uploads/results/' . $fileToDelete);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            return response()->json(['success' => true, 'message' => 'تم حذف الملف بنجاح']);
        }
        
        return response()->json(['success' => false, 'message' => 'الملف غير موجود'], 404);
    }
}