@extends('layouts.app')

@section('title', 'أنواع الفحوصات الطبية')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">أنواع الفحوصات الطبية</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('lab.dashboard') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-right"></i> العودة للوحة التحكم
                        </a>
                        <a href="{{ route('lab.type-medical-tests.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> إضافة نوع جديد
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Filters --}}
                    <div class="row mb-3 g-2">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('lab.type-medical-tests.index') }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2"
                                       placeholder="بحث بالاسم" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">بحث</button>
                                <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                            </form>
                        </div>
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('lab.type-medical-tests.index') }}" class="d-flex">
                                <input type="number" name="min_price" class="form-control me-2"
                                       placeholder="أدنى سعر" value="{{ request('min_price') }}" step="0.01" min="0">
                                <input type="number" name="max_price" class="form-control me-2"
                                       placeholder="أعلى سعر" value="{{ request('max_price') }}" step="0.01" min="0">
                                <button type="submit" class="btn btn-secondary me-2">تصفية</button>
                                <a href="{{ route('lab.type-medical-tests.index') }}" class="btn btn-outline-secondary">مسح</a>
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم الفحص</th>
                                    <th>السعر</th>
                                    <th>تاريخ الإضافة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($medicalTests as $test)
                                    <tr>
                                        <td>{{ $test->id }}</td>
                                        <td>{{ $test->name }}</td>
                                        <td>{{ $test->formatted_price }}</td>
                                        <td>{{ $test->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('lab.type-medical-tests.edit', $test) }}"
                                                   class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i> تعديل
                                                </a>
                                                <form action="{{ route('lab.type-medical-tests.destroy', $test) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i> حذف
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">لا توجد أنواع فحوصات مضافة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $medicalTests->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
