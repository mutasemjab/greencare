@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.medical_test_details') }}</h3>
                        <div>
                            @can('medical-test-edit')
                                <a href="{{ route('medical-tests.edit', $medicalTest) }}" class="btn btn-warning btn-sm me-2">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('medical-tests.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-flask"></i> {{ __('messages.test_information') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.name_en') }}:</strong>
                                            <p>{{ $medicalTest->name_en }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.name_ar') }}:</strong>
                                            <p>{{ $medicalTest->name_ar }}</p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.price') }}:</strong>
                                            <p>{{ $medicalTest->price }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.lab') }}:</strong>
                                            <p>
                                                @if($medicalTest->lab)
                                                    <a href="{{ route('labs.show', $medicalTest->lab) }}">{{ $medicalTest->lab->name }}</a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    @if($medicalTest->description_en || $medicalTest->description_ar)
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <strong>{{ __('messages.description') }}:</strong>
                                                <p>{{ $medicalTest->description_en ?? $medicalTest->description_ar }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.created_at') }}:</strong>
                                            <p>{{ $medicalTest->created_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.updated_at') }}:</strong>
                                            <p>{{ $medicalTest->updated_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle"></i> {{ __('messages.summary') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <strong>{{ __('messages.test_name') }}:</strong><br>
                                            {{ $medicalTest->name_en }}
                                        </li>
                                        <li class="mb-2">
                                            <strong>{{ __('messages.price') }}:</strong><br>
                                            <span class="badge bg-success">{{ $medicalTest->price }}</span>
                                        </li>
                                        @if($medicalTest->lab)
                                            <li class="mb-2">
                                                <strong>{{ __('messages.laboratory') }}:</strong><br>
                                                {{ $medicalTest->lab->name }}
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
