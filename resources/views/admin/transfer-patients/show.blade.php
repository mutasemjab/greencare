@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('messages.transfer_patient_details') }}</h4>
                    <div>
                        @can('transfer-patient-edit')
                        <a href="{{ route('transfer-patients.edit', $transferPatient) }}" class="btn btn-warning btn-sm me-2">
                            {{ __('messages.edit') }}
                        </a>
                        @endcan
                        <a href="{{ route('transfer-patients.index') }}" class="btn btn-secondary btn-sm">
                            {{ __('messages.back_to_list') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Transfer Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">{{ __('messages.transfer_information') }}</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">{{ __('messages.id') }}:</th>
                                            <td>{{ $transferPatient->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('messages.date') }}:</th>
                                            <td>{{ $transferPatient->date_of_transfer->format('Y-m-d') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('messages.time') }}:</th>
                                            <td>{{ $transferPatient->time_of_transfer ? $transferPatient->time_of_transfer->format('H:i') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('messages.created_at') }}:</th>
                                            <td>{{ $transferPatient->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('messages.updated_at') }}:</th>
                                            <td>{{ $transferPatient->updated_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">{{ __('messages.patient') }}</h6>
                                </div>
                                <div class="card-body">
                                    @if($transferPatient->user)
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">{{ __('messages.name') }}:</th>
                                                <td><strong>{{ $transferPatient->user->name }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('messages.email') }}:</th>
                                                <td>{{ $transferPatient->user->email }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('messages.phone') }}:</th>
                                                <td>{{ $transferPatient->user->phone ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            {{ __('messages.no_patient_assigned') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- From Location -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">{{ __('messages.from_location') }}</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">{{ __('messages.address') }}:</th>
                                            <td>
                                                @if($transferPatient->from_address)
                                                    <a href="{{ $transferPatient->from_address }}" target="_blank" rel="noopener">
                                                        {{ Str::limit($transferPatient->from_address, 40) }}
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('messages.location') }}:</th>
                                            <td>
                                                <span class="badge {{ $transferPatient->from_place == 1 ? 'bg-success' : 'bg-info' }}">
                                                    {{ $transferPatient->from_place_text }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($transferPatient->from_lat && $transferPatient->from_lng)
                                        <tr>
                                            <th>{{ __('messages.coordinates') }}:</th>
                                            <td>
                                                <small class="text-muted">
                                                    {{ number_format($transferPatient->from_lat, 6) }},
                                                    {{ number_format($transferPatient->from_lng, 6) }}
                                                </small>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- To Location -->
                        <div class="col-md-6">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">{{ __('messages.to_location') }}</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">{{ __('messages.address') }}:</th>
                                            <td>
                                                @if($transferPatient->to_address)
                                                    <a href="{{ $transferPatient->to_address }}" target="_blank" rel="noopener">
                                                        {{ Str::limit($transferPatient->to_address, 40) }}
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('messages.location') }}:</th>
                                            <td>
                                                <span class="badge {{ $transferPatient->to_place == 1 ? 'bg-success' : 'bg-info' }}">
                                                    {{ $transferPatient->to_place_text }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($transferPatient->to_lat && $transferPatient->to_lng)
                                        <tr>
                                            <th>{{ __('messages.coordinates') }}:</th>
                                            <td>
                                                <small class="text-muted">
                                                    {{ number_format($transferPatient->to_lat, 6) }},
                                                    {{ number_format($transferPatient->to_lng, 6) }}
                                                </small>
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($transferPatient->note)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">{{ __('messages.note') }}</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $transferPatient->note }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        @can('transfer-patient-delete')
                        <form action="{{ route('transfer-patients.destroy', $transferPatient) }}"
                              method="POST"
                              style="display: inline-block;"
                              onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger me-2">
                                <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                            </button>
                        </form>
                        @endcan
                        @can('transfer-patient-edit')
                        <a href="{{ route('transfer-patients.edit', $transferPatient) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                        </a>
                        @endcan
                        <a href="{{ route('transfer-patients.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
