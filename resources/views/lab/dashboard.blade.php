{{-- resources/views/lab/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>مرحباً، {{ $lab->name }}</h2>
        <p class="text-muted">آخر تسجيل دخول: {{ now()->format('Y-m-d H:i') }}</p>
    </div>
</div>

{{-- الإحصائيات --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="bi bi-calendar-check fs-1"></i>
                <h3 class="mt-3">{{ $stats['total_appointments'] }}</h3>
                <p class="mb-0">إجمالي المواعيد</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="bi bi-clock-history fs-1"></i>
                <h3 class="mt-3">{{ $stats['pending'] }}</h3>
                <p class="mb-0">قيد الانتظار</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="bi bi-gear fs-1"></i>
                <h3 class="mt-3">{{ $stats['processing'] }}</h3>
                <p class="mb-0">قيد المعالجة</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="bi bi-check-circle fs-1"></i>
                <h3 class="mt-3">{{ $stats['finished'] }}</h3>
                <p class="mb-0">منتهية</p>
            </div>
        </div>
    </div>
</div>

{{-- روابط سريعة --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">روابط سريعة</h5>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('lab.appointments.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-list-ul"></i> جميع المواعيد
                    </a>
                    <a href="{{ route('lab.appointments.index', ['status' => 'pending']) }}" class="btn btn-outline-warning">
                        <i class="bi bi-clock"></i> المواعيد المعلقة
                    </a>
                    <a href="{{ route('lab.appointments.index', ['status' => 'processing']) }}" class="btn btn-outline-info">
                        <i class="bi bi-gear"></i> قيد المعالجة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- آخر المواعيد --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">آخر المواعيد</h5>
                <a href="{{ route('lab.appointments.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
            </div>
            <div class="card-body">
                @if($recentMedicalTests->isEmpty() && $recentHomeXrays->isEmpty())
                    <p class="text-center text-muted">لا توجد مواعيد حالياً</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>النوع</th>
                                    <th>المريض</th>
                                    <th>الخدمة</th>
                                    <th>التاريخ</th>
                                    <th>الحالة</th>
                                    <th>الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentMedicalTests as $appointment)
                                    <tr>
                                        <td><span class="badge bg-info">فحص طبي</span></td>
                                        <td>{{ $appointment->user->name }}</td>
                                        <td>{{ $appointment->typeMedicalTest->name }}</td>
                                        <td>{{ $appointment->date_of_appointment->format('Y-m-d') }}</td>
                                        <td>
                                            @include('lab.appointments.partials.status-badge', ['status' => $appointment->status])
                                        </td>
                                        <td>
                                            <a href="{{ route('lab.appointments.show', ['type' => 'medical_test', 'id' => $appointment->id]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> عرض
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                
                                @foreach($recentHomeXrays as $appointment)
                                    <tr>
                                        <td><span class="badge bg-success">أشعة منزلية</span></td>
                                        <td>{{ $appointment->user->name }}</td>
                                        <td>{{ $appointment->typeHomeXray->name }}</td>
                                        <td>{{ $appointment->date_of_appointment->format('Y-m-d') }}</td>
                                        <td>
                                            @include('lab.appointments.partials.status-badge', ['status' => $appointment->status])
                                        </td>
                                        <td>
                                            <a href="{{ route('lab.appointments.show', ['type' => 'home_xray', 'id' => $appointment->id]) }}" 
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
    </div>
</div>
@endsection