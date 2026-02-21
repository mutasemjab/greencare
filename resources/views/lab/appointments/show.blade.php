{{-- resources/views/lab/appointments/show.blade.php --}}
@extends('layouts.app')

@section('title', 'تفاصيل الموعد')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>تفاصيل الموعد #{{ $appointment->id }}</h2>
            <a href="{{ route('lab.appointments.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right"></i> رجوع
            </a>
        </div>
    </div>
</div>

{{-- الرسائل --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    {{-- معلومات الموعد --}}
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">معلومات الموعد</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>نوع الخدمة:</strong>
                        @if($type == 'medical_test')
                            <span class="badge bg-info">فحص طبي</span>
                        @else
                            <span class="badge bg-success">أشعة منزلية</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>الحالة:</strong>
                        @include('lab.appointments.partials.status-badge', ['status' => $appointment->status])
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>اسم الخدمة:</strong>
                        @if($type == 'medical_test')
                            <p class="mb-0">{{ $appointment->typeMedicalTest->name }}</p>
                        @else
                            <p class="mb-0">{{ $appointment->typeHomeXray->name }}</p>
                            @if($appointment->typeHomeXray->parent)
                                <small class="text-muted">تحت: {{ $appointment->typeHomeXray->parent->name }}</small>
                            @endif
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>تاريخ الموعد:</strong>
                        <p class="mb-0">{{ $appointment->date_of_appointment->format('Y-m-d') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>وقت الموعد:</strong>
                        <p class="mb-0">
                            @if($appointment->time_of_appointment)
                                {{ $appointment->time_of_appointment->format('H:i') }}
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>السعر:</strong>
                        @if($type == 'medical_test')
                            <p class="mb-0">{{ number_format($appointment->typeMedicalTest->price, 2) }} دينار</p>
                        @else
                            <p class="mb-0">{{ number_format($appointment->typeHomeXray->price, 2) }} دينار</p>
                        @endif
                    </div>
                </div>
                
                @if($appointment->address)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>العنوان:</strong>
                            <p class="mb-0">{{ $appointment->address }}</p>
                        </div>
                    </div>
                @endif
                
                @if($appointment->lat && $appointment->lng)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>الموقع:</strong>
                            <p class="mb-0">
                                <a href="https://www.google.com/maps?q={{ $appointment->lat }},{{ $appointment->lng }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-geo-alt"></i> عرض على الخريطة
                                </a>
                            </p>
                        </div>
                    </div>
                @endif
                
                @if($appointment->note)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>ملاحظات:</strong>
                            <p class="mb-0">{{ $appointment->note }}</p>
                        </div>
                    </div>
                @endif
                
                @if($appointment->room)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>رمز الغرفة:</strong>
                            <p class="mb-0">
                                <span class="badge bg-secondary">{{ $appointment->room->code }}</span>
                                @if($appointment->room->discount > 0)
                                    <small class="text-success">(خصم {{ $appointment->room->discount }}%)</small>
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
                
                <div class="row">
                    <div class="col-md-6">
                        <strong>تاريخ الإنشاء:</strong>
                        <p class="mb-0">{{ $appointment->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>آخر تحديث:</strong>
                        <p class="mb-0">{{ $appointment->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- رفع النتائج --}}
        @if($appointment->status != 'cancelled')
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">رفع النتائج</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('lab.appointments.upload-results', ['type' => $type, 'id' => $appointment->id]) }}" 
                          method="POST" 
                          enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">الملفات (PDF, JPG, PNG) <span class="text-danger">*</span></label>
                            <input type="file" 
                                   name="files[]" 
                                   class="form-control @error('files') is-invalid @enderror @error('files.*') is-invalid @enderror" 
                                   multiple 
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   required>
                            <small class="text-muted">يمكنك رفع حتى 10 ملفات، الحد الأقصى لكل ملف 10 ميجا</small>
                            @error('files')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" 
                                      class="form-control @error('notes') is-invalid @enderror" 
                                      rows="3"
                                      placeholder="أضف ملاحظات إضافية...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-cloud-upload"></i> رفع النتائج
                        </button>
                    </form>
                </div>
            </div>
        @endif
        
        {{-- النتائج المرفوعة --}}
        @if($appointment->result)
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">النتائج المرفوعة</h5>
                </div>
                <div class="card-body">
                    @if($appointment->result->notes)
                        <div class="mb-3">
                            <strong>الملاحظات:</strong>
                            <p class="mb-0">{{ $appointment->result->notes }}</p>
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <strong>تاريخ الإنجاز:</strong>
                        <p class="mb-0">{{ $appointment->result->completed_at ? $appointment->result->completed_at->format('Y-m-d H:i') : '-' }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <strong>الملفات:</strong>
                        <div class="row g-2 mt-2">
                            @forelse($appointment->result->file_urls as $index => $fileUrl)
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body text-center p-2">
                                            @php
                                                $fileName = basename($fileUrl);
                                                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                                            @endphp
                                            
                                            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                                                <img src="{{ $fileUrl }}" 
                                                     alt="Result" 
                                                     class="img-fluid mb-2" 
                                                     style="max-height: 150px; object-fit: cover;">
                                            @else
                                                <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
                                            @endif
                                            
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ $fileUrl }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ $fileUrl }}" 
                                                   download 
                                                   class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteFile({{ $appointment->result->id }}, '{{ $appointment->result->files[$index] }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">لا توجد ملفات</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    {{-- معلومات المريض وتحديث الحالة --}}
    <div class="col-md-4">
        {{-- معلومات المريض --}}
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">معلومات المريض</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    @if($appointment->user->photo)
                        <img src="{{ asset($appointment->user->photo) }}" 
                             alt="{{ $appointment->user->name }}" 
                             class="rounded-circle" 
                             style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white" 
                             style="width: 100px; height: 100px; font-size: 40px;">
                            {{ substr($appointment->user->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                
                <div class="mb-2">
                    <strong>الاسم:</strong>
                    <p class="mb-0">{{ $appointment->user->name }}</p>
                </div>
                
                <div class="mb-2">
                    <strong>الهاتف:</strong>
                    <p class="mb-0">
                        <a href="tel:{{ $appointment->user->phone }}">{{ $appointment->user->phone }}</a>
                    </p>
                </div>
                
                @if($appointment->user->email)
                    <div class="mb-2">
                        <strong>البريد الإلكتروني:</strong>
                        <p class="mb-0">
                            <a href="mailto:{{ $appointment->user->email }}">{{ $appointment->user->email }}</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
        
        {{-- تحديث الحالة --}}
        @if($appointment->status != 'cancelled' && $appointment->status != 'finished')
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">تحديث الحالة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('lab.appointments.update-status', ['type' => $type, 'id' => $appointment->id]) }}" 
                          method="POST"
                          id="updateStatusForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">الحالة الجديدة <span class="text-danger">*</span></label>
                            <select name="status" 
                                    class="form-select @error('status') is-invalid @enderror" 
                                    id="statusSelect"
                                    required>
                                <option value="">اختر الحالة</option>
                                <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                                <option value="processing" {{ $appointment->status == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                <option value="finished" {{ $appointment->status == 'finished' ? 'selected' : '' }}>منتهي</option>
                                <option value="cancelled">ملغي</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3" id="cancellationReasonDiv" style="display: none;">
                            <label class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                            <textarea name="cancellation_reason" 
                                      class="form-control @error('cancellation_reason') is-invalid @enderror" 
                                      rows="3"
                                      placeholder="اذكر سبب إلغاء الموعد..."></textarea>
                            @error('cancellation_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> تحديث الحالة
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// إظهار/إخفاء حقل سبب الإلغاء
document.getElementById('statusSelect')?.addEventListener('change', function() {
    const cancellationDiv = document.getElementById('cancellationReasonDiv');
    if (this.value === 'cancelled') {
        cancellationDiv.style.display = 'block';
        cancellationDiv.querySelector('textarea').required = true;
    } else {
        cancellationDiv.style.display = 'none';
        cancellationDiv.querySelector('textarea').required = false;
    }
});

// حذف ملف
function deleteFile(resultId, filePath) {
    if (!confirm('هل أنت متأكد من حذف هذا الملف؟')) {
        return;
    }
    
    fetch(`{{ route('lab.appointments.delete-file', ':resultId') }}`.replace(':resultId', resultId), {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ file_path: filePath })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'حدث خطأ أثناء حذف الملف');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء حذف الملف');
    });
}
</script>
@endpush