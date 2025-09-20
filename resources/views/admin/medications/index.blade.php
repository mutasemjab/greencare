@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.medications') }}</h3>
                    @can('medication-add')
                        <a href="{{ route('medications.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.add_medication') }}
                        </a>
                    @endcan
                </div>

                <div class="card-body">
                 

                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('medications.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="{{ __('messages.search_medications') }}"
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="room_id" class="form-control">
                                    <option value="">{{ __('messages.all_rooms') }}</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                                            {{ $room->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="patient_id" class="form-control">
                                    <option value="">{{ __('messages.all_patients') }}</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="active" class="form-control">
                                    <option value="">{{ __('messages.all_status') }}</option>
                                    <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                    <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> {{ __('messages.search') }}
                                    </button>
                                    <a href="{{ route('medications.index') }}" class="btn btn-outline-secondary">
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
                                    <th>{{ __('messages.medication_name') }}</th>
                                    <th>{{ __('messages.patient') }}</th>
                                    <th>{{ __('messages.room') }}</th>
                                    <th>{{ __('messages.dosage') }}</th>
                                    <th>{{ __('messages.schedules') }}</th>
                                    <th>{{ __('messages.compliance_rate') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($medications as $medication)
                                    <tr>
                                        <td>{{ $loop->iteration + ($medications->currentPage() - 1) * $medications->perPage() }}</td>
                                        <td>
                                            <strong>{{ $medication->name }}</strong>
                                            @if($medication->notes)
                                                <br><small class="text-muted">{{ Str::limit($medication->notes, 30) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($medication->patient->photo)
                                                    <img src="{{ asset('storage/' . $medication->patient->photo) }}" 
                                                         class="rounded-circle mr-2" 
                                                         width="30" height="30"
                                                         alt="{{ $medication->patient->name }}">
                                                @else
                                                    <div class="rounded-circle mr-2 d-flex align-items-center justify-content-center bg-warning text-white" 
                                                         style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                        {{ substr($medication->patient->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="font-weight-bold">{{ $medication->patient->name }}</div>
                                                    <small class="text-muted">{{ $medication->patient->phone }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($medication->room)
                                                <span class="badge badge-info">{{ $medication->room->title }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($medication->dosage)
                                                <span class="badge badge-secondary">{{ $medication->dosage }}</span>
                                            @endif
                                            @if($medication->quantity)
                                                <br><small class="text-muted">{{ $medication->quantity }} {{ __('messages.per_dose') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $medication->schedules->count() }} {{ __('messages.times') }}</span>
                                            <div class="mt-1">
                                                @foreach($medication->schedules->take(2) as $schedule)
                                                    <small class="d-block text-muted">{{ $schedule->formatted_time }} ({{ $schedule->frequency_text }})</small>
                                                @endforeach
                                                @if($medication->schedules->count() > 2)
                                                    <small class="text-muted">+{{ $medication->schedules->count() - 2 }} {{ __('messages.more') }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $rate = $medication->compliance_rate;
                                                $class = $rate >= 80 ? 'success' : ($rate >= 60 ? 'warning' : 'danger');
                                            @endphp
                                            <span class="badge badge-{{ $class }}">{{ $rate }}%</span>
                                        </td>
                                        <td>
                                            @can('medication-edit')
                                                <form action="{{ route('medications.toggle-active', $medication) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-{{ $medication->active ? 'success' : 'secondary' }} btn-toggle-status"
                                                            title="{{ $medication->active ? __('messages.active') : __('messages.inactive') }}">
                                                        <i class="fas fa-{{ $medication->active ? 'toggle-on' : 'toggle-off' }}"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge badge-{{ $medication->active ? 'success' : 'secondary' }}">
                                                    {{ $medication->active ? __('messages.active') : __('messages.inactive') }}
                                                </span>
                                            @endcan
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('medication-table')
                                                    <a href="{{ route('medications.show', $medication) }}" 
                                                       class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('medication-edit')
                                                    <a href="{{ route('medications.edit', $medication) }}" 
                                                       class="btn btn-sm btn-warning" title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('medication-delete')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="{{ __('messages.delete') }}"
                                                            data-toggle="modal" 
                                                            data-target="#deleteModal{{ $medication->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>

                                            @can('medication-delete')
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal{{ $medication->id }}" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('messages.confirm_delete') }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                {{ __('messages.are_you_sure_delete_medication') }} "<strong>{{ $medication->name }}</strong>" {{ __('messages.for_patient') }} "<strong>{{ $medication->patient->name }}</strong>"?
                                                                <br><small class="text-muted">{{ __('messages.delete_medication_warning') }}</small>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                    {{ __('messages.cancel') }}
                                                                </button>
                                                                <form action="{{ route('medications.destroy', $medication) }}" method="POST" class="d-inline">
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
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-pills fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">{{ __('messages.no_medications_found') }}</p>
                                                @can('medication-add')
                                                    <a href="{{ route('medications.create') }}" class="btn btn-primary">
                                                        {{ __('messages.add_first_medication') }}
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($medications->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $medications->appends(request()->query())->links() }}
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

    // Confirm status toggle
    $('.btn-toggle-status').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var isActive = $(this).hasClass('btn-success');
        var action = isActive ? '{{ __("messages.deactivate") }}' : '{{ __("messages.activate") }}';
        
        if (confirm('{{ __("messages.are_you_sure") }} ' + action.toLowerCase() + ' {{ __("messages.this_medication") }}?')) {
            form.submit();
        }
    });
});
</script>
@endpush
