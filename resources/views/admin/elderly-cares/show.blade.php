@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.elderly_care_details') }}</h3>
                        <div>
                            @can('elderly-care-edit')
                                <a href="{{ route('elderly-cares.edit', $elderlyCare) }}" class="btn btn-warning btn-sm me-2">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('elderly-cares.index') }}" class="btn btn-secondary btn-sm">
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
                                        <i class="fas fa-home"></i> {{ __('messages.elderly_care_information') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.name_en') }}:</strong>
                                            <p>{{ $elderlyCare->name_en ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.name_ar') }}:</strong>
                                            <p>{{ $elderlyCare->name_ar ?? '-' }}</p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.price') }}:</strong>
                                            <p>{{ $elderlyCare->price ?? '-' }}</p>
                                        </div>
                                        @if(isset($elderlyCare->provider))
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.provider') }}:</strong>
                                            <p>
                                                <a href="{{ route('providers.edit', $elderlyCare->provider) }}">{{ $elderlyCare->provider->name }}</a>
                                            </p>
                                        </div>
                                        @endif
                                    </div>

                                    @if($elderlyCare->description_en || $elderlyCare->description_ar)
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <strong>{{ __('messages.description') }}:</strong>
                                                <p>{{ $elderlyCare->description_en ?? $elderlyCare->description_ar }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.created_at') }}:</strong>
                                            <p>{{ $elderlyCare->created_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.updated_at') }}:</strong>
                                            <p>{{ $elderlyCare->updated_at->format('Y-m-d H:i') }}</p>
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
                                            <strong>{{ __('messages.name') }}:</strong><br>
                                            {{ $elderlyCare->name_en }}
                                        </li>
                                        <li class="mb-2">
                                            <strong>{{ __('messages.price') }}:</strong><br>
                                            <span class="badge bg-success">{{ $elderlyCare->price ?? '-' }}</span>
                                        </li>
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
