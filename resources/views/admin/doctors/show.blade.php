@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.doctor_details') }}</h3>
                        <div>
                            @can('doctor-edit')
                                <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-warning btn-sm me-2">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('doctors.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Doctor Profile -->
                    <div class="row">
                        <div class="col-md-3 text-center">
                            @if($doctor->photo)
                                <img src="{{ asset('assets/admin/uploads/' . $doctor->photo) }}"
                                     alt="{{ $doctor->name }}"
                                     class="img-thumbnail rounded-circle mb-3"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white mx-auto mb-3"
                                     style="width: 150px; height: 150px; font-size: 3rem;">
                                    {{ substr($doctor->name, 0, 1) }}
                                </div>
                            @endif

                            <h4>{{ $doctor->name }}</h4>
                            <p class="text-muted">{{ __('messages.doctor') }}</p>

                            <span class="badge bg-{{ $doctor->activate == 1 ? 'success' : 'danger' }} fs-6">
                                {{ $doctor->active_status_text }}
                            </span>
                        </div>

                        <div class="col-md-9">
                            <!-- Personal Information -->
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user"></i> {{ __('messages.personal_information') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>{{ __('messages.name') }}:</strong></td>
                                                    <td>{{ $doctor->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('messages.phone') }}:</strong></td>
                                                    <td>
                                                        <a href="tel:{{ $doctor->phone }}">{{ $doctor->phone }}</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('messages.email') }}:</strong></td>
                                                    <td>
                                                        @if($doctor->email)
                                                            <a href="mailto:{{ $doctor->email }}">{{ $doctor->email }}</a>
                                                        @else
                                                            <span class="text-muted">{{ __('messages.not_available') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>{{ __('messages.date_of_birth') }}:</strong></td>
                                                    <td>{{ $doctor->date_of_birth ? $doctor->date_of_birth->format('Y-m-d') : '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('messages.gender') }}:</strong></td>
                                                    <td>
                                                        <span class="badge bg-{{ $doctor->gender == 1 ? 'info' : 'pink' }}">
                                                            {{ $doctor->gender_text }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('messages.age') }}:</strong></td>
                                                    <td>{{ $doctor->date_of_birth ? $doctor->date_of_birth->age : '-' }} {{ __('messages.years') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Information -->
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle"></i> {{ __('messages.account_information') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>{{ __('messages.user_type') }}:</strong></td>
                                                    <td><span class="badge bg-secondary">{{ $doctor->user_type_text }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('messages.status') }}:</strong></td>
                                                    <td>
                                                        <span class="badge bg-{{ $doctor->activate == 1 ? 'success' : 'danger' }}">
                                                            {{ $doctor->active_status_text }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>{{ __('messages.created_at') }}:</strong></td>
                                                    <td>{{ $doctor->created_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('messages.updated_at') }}:</strong></td>
                                                    <td>{{ $doctor->updated_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Families -->
                            @if($doctor->families->count() > 0)
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-users"></i> {{ __('messages.associated_families') }}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($doctor->families as $family)
                                                <div class="col-md-4 mb-3">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h6 class="card-title">{{ $family->name }}</h6>
                                                            <p class="card-text text-muted">
                                                                <small>{{ $family->address ?? '-' }}</small>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
