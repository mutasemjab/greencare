{{-- resources/views/lab/appointments/index.blade.php --}}
@extends('layouts.app')

@section('title', 'المواعيد')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>إدارة المواعيد</h2>
    </div>
</div>

{{-- الفلاتر --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('lab.appointments.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">نوع الخدمة</label>
                    <select name="type" class="form-select">
                        <option value="all" {{ $type == 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="medical_test" {{ $type == 'medical_test' ? 'selected' : '' }}>فحوصات طبية</option>
                        <option value="home_xray" {{ $type == 'home_xray' ? 'selected' : '' }}>أشعة منزلية</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">الكل</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="confirmed" {{ $status == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                        <option value="processing" {{ $status == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                        <option value="finished" {{ $status == 'finished' ? 'selected' : '' }}>منتهي</option>
                        <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">بحث</label>
                    <input type="text" name="search" class="form-control" placeholder="اسم أو رقم" value="{{ $search }}">
                </div>
                
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> بحث
                    </button>
                    <a href="{{ route('lab.appointments.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> مسح
                    </a>
                </div>
            </div>
        </form>
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

{{-- الجدول --}}
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">المواعيد ({{ $appointments->count() }})</h5>
    </div>
    <div class="card-body">
        @if($appointments->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-3">لا توجد مواعيد</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>النوع</th>
                            <th>المريض</th>
                            <th>الهاتف</th>
                            <th>الخدمة</th>
                            <th>التاريخ</th>
                            <th>الوقت</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->id }}</td>
                                <td>
                                    @if($appointment->appointment_type == 'medical_test')
                                        <span class="badge bg-info">فحص طبي</span>
                                    @else
                                        <span class="badge bg-success">أشعة منزلية</span>
                                    @endif
                                </td>
                                <td>{{ $appointment->user->name }}</td>
                                <td>{{ $appointment->user->phone }}</td>
                                <td>
                                    @if($appointment->appointment_type == 'medical_test')
                                        {{ $appointment->typeMedicalTest->name }}
                                    @else
                                        {{ $appointment->typeHomeXray->name }}
                                    @endif
                                </td>
                                <td>{{ $appointment->date_of_appointment->format('Y-m-d') }}</td>
                                <td>
                                    @if($appointment->time_of_appointment)
                                        {{ $appointment->time_of_appointment->format('H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @include('lab.appointments.partials.status-badge', ['status' => $appointment->status])
                                </td>
                                <td>
                                    <a href="{{ route('lab.appointments.show', ['type' => $appointment->appointment_type, 'id' => $appointment->id]) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> عرض
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection