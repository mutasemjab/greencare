@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.request_nurse_details') }}</h3>
                        <div>
                            @can('typeRequestNurse-edit')
                                <a href="{{ route('request-nurses.edit', $requestNurse) }}" class="btn btn-warning btn-sm me-2">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('request-nurses.index') }}" class="btn btn-secondary btn-sm">
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
                                        <i class="fas fa-user-nurse"></i> {{ __('messages.request_nurse_information') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.type_of_service') }}:</strong>
                                            <p>
                                                <span class="badge bg-info">
                                                    {{ __('messages.' . $requestNurse->type_of_service) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.price') }}:</strong>
                                            <p>{{ $requestNurse->formatted_price }}</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.created_at') }}:</strong>
                                            <p>{{ $requestNurse->created_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.updated_at') }}:</strong>
                                            <p>{{ $requestNurse->updated_at->format('Y-m-d H:i') }}</p>
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
                                            <strong>{{ __('messages.type_of_service') }}:</strong><br>
                                            {{ __('messages.' . $requestNurse->type_of_service) }}
                                        </li>
                                        <li class="mb-2">
                                            <strong>{{ __('messages.price') }}:</strong><br>
                                            <span class="badge bg-success">{{ $requestNurse->formatted_price }}</span>
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
