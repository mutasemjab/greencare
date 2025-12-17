@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.patient_diagnoses') }}</h3>
                </div>

                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('diagnosis.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="{{ __('messages.search_patient_provider') }}"
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('messages.all_status') }}</option>
                                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>{{ __('messages.accepted') }}</option>
                                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>{{ __('messages.on_the_way') }}</option>
                                    <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>{{ __('messages.delivered') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="has_diagnosis" class="form-control">
                                    <option value="">{{ __('messages.all_diagnoses') }}</option>
                                    <option value="1" {{ request('has_diagnosis') == '1' ? 'selected' : '' }}>{{ __('messages.with_diagnosis') }}</option>
                                    <option value="0" {{ request('has_diagnosis') == '0' ? 'selected' : '' }}>{{ __('messages.without_diagnosis') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> {{ __('messages.search') }}
                                    </button>
                                    <a href="{{ route('diagnosis.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> {{ __('messages.clear') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.patient_name') }}</th>
                                    <th>{{ __('messages.phone') }}</th>
                                    <th>{{ __('messages.provider') }}</th>
                                    <th>{{ __('messages.appointment_date') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.diagnosis_status') }}</th>
                                    <th>{{ __('messages.diagnosed_by') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appointment)
                                    <tr>
                                        <td>{{ $loop->iteration + ($appointments->currentPage() - 1) * $appointments->perPage() }}</td>
                                        <td>
                                            <strong>{{ $appointment->name_of_patient }}</strong>
                                            @if($appointment->user)
                                                <br><small class="text-muted">{{ $appointment->user->name }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $appointment->phone_of_patient }}</td>
                                        <td>
                                            @if($appointment->provider)
                                                {{ $appointment->provider->name }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($appointment->date_of_appointment)
                                                {{ $appointment->date_of_appointment->format('Y-m-d') }}
                                                @if($appointment->time_of_appointment)
                                                    <br><small>{{ $appointment->time_of_appointment }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $appointment->status_badge }}">
                                                {{ $appointment->status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($appointment->diagnosis)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check"></i> {{ __('messages.diagnosed') }}
                                                </span>
                                                <br><small class="text-muted">
                                                    {{ $appointment->diagnosis->created_at->format('Y-m-d H:i') }}
                                                </small>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> {{ __('messages.pending_diagnosis') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($appointment->diagnosis)
                                                {{ $appointment->diagnosis->diagnosedBy->name }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if($appointment->diagnosis)
                                                    @can('diagnosis-table')
                                                        <a href="{{ route('diagnosis.show', $appointment->diagnosis) }}" 
                                                           class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endcan
                                                    @can('diagnosis-edit')
                                                        <a href="{{ route('diagnosis.edit', $appointment->diagnosis) }}" 
                                                           class="btn btn-sm btn-warning" title="{{ __('messages.edit') }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('diagnosis-delete')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="{{ __('messages.delete') }}"
                                                                data-toggle="modal" 
                                                                data-target="#deleteModal{{ $appointment->diagnosis->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endcan
                                                @else
                                                    @can('diagnosis-add')
                                                        <a href="{{ route('diagnosis.create.appointment', $appointment->id) }}" 
                                                           class="btn btn-sm btn-success" title="{{ __('messages.add_diagnosis') }}">
                                                            <i class="fas fa-plus"></i> {{ __('messages.diagnose') }}
                                                        </a>
                                                    @endcan
                                                @endif
                                            </div>

                                            @if($appointment->diagnosis)
                                                @can('diagnosis-delete')
                                                    <!-- Delete Modal -->
                                                    <div class="modal fade" id="deleteModal{{ $appointment->diagnosis->id }}" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">{{ __('messages.confirm_delete') }}</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    {{ __('messages.are_you_sure_delete_diagnosis') }}
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                        {{ __('messages.cancel') }}
                                                                    </button>
                                                                    <form action="{{ route('diagnosis.destroy', $appointment->diagnosis) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger">
                                                                            {{ __('messages.delete') }}
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-stethoscope fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">{{ __('messages.no_appointments_found') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($appointments->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $appointments->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush