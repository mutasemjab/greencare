@extends('layouts.admin')

@section('title', __('messages.all_appointments'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.all_appointments') }}</h3>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="{{ route('appointments.index') }}" id="filterForm">
                                <div class="row">
                                    <!-- Appointment Type Filter -->
                                    <div class="col-md-2 mb-3">
                                        <label for="type" class="form-label">{{ __('messages.appointment_type') }}</label>
                                        <select name="type" id="type" class="form-control">
                                            <option value="all" {{ $type == 'all' ? 'selected' : '' }}>{{ __('messages.all_types') }}</option>
                                            <option value="elderly_care" {{ $type == 'elderly_care' ? 'selected' : '' }}>{{ __('messages.elderly_care') }}</option>
                                            <option value="home_xray" {{ $type == 'home_xray' ? 'selected' : '' }}>{{ __('messages.home_xray') }}</option>
                                            <option value="medical_test" {{ $type == 'medical_test' ? 'selected' : '' }}>{{ __('messages.medical_test') }}</option>
                                        </select>
                                    </div>

                                    <!-- Date From -->
                                    <div class="col-md-2 mb-3">
                                        <label for="date_from" class="form-label">{{ __('messages.date_from') }}</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}">
                                    </div>

                                    <!-- Date To -->
                                    <div class="col-md-2 mb-3">
                                        <label for="date_to" class="form-label">{{ __('messages.date_to') }}</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}">
                                    </div>

                                    <!-- User Filter -->
                                    <div class="col-md-2 mb-3">
                                        <label for="user_id" class="form-label">{{ __('messages.user') }}</label>
                                        <select name="user_id" id="user_id" class="form-control">
                                            <option value="">{{ __('messages.all_users') }}</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Search -->
                                    <div class="col-md-2 mb-3">
                                        <label for="search" class="form-label">{{ __('messages.search_notes') }}</label>
                                        <input type="text" name="search" id="search" class="form-control" 
                                               placeholder="{{ __('messages.search_in_notes') }}" value="{{ $search }}">
                                    </div>

                                    <!-- Filter Buttons -->
                                    <div class="col-md-2 mb-3 d-flex align-items-end">
                                        <div class="w-100">
                                            <button type="submit" class="btn btn-primary w-100 mb-2">{{ __('messages.filter') }}</button>
                                            <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary w-100">{{ __('messages.clear') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong>{{ __('messages.total_appointments') }}:</strong> {{ $pagination['total'] }}
                                @if($type !== 'all')
                                    - <strong>{{ __('messages.filtered_by') }}:</strong> {{ __('messages.' . $type) }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.id') }}</th>
                                    <th>{{ __('messages.appointment_type') }}</th>
                                    <th>{{ __('messages.service_name') }}</th>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.appointment_date') }}</th>
                                    <th>{{ __('messages.appointment_time') }}</th>
                                    <th>{{ __('messages.price') }}</th>
                                    <th>{{ __('messages.note') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment['id'] }}</td>
                                        <td>
                                            @switch($appointment['type'])
                                                @case('elderly_care')
                                                    <span class="badge bg-primary">{{ __('messages.elderly_care') }}</span>
                                                    @break
                                                @case('home_xray')
                                                    <span class="badge bg-success">{{ __('messages.home_xray') }}</span>
                                                    @break
                                                @case('medical_test')
                                                    <span class="badge bg-warning">{{ __('messages.medical_test') }}</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>{{ $appointment['service_name'] }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $appointment['user_name'] }}</strong><br>
                                                <small class="text-muted">{{ $appointment['user_email'] }}</small>
                                            </div>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($appointment['date_of_appointment'])->format('Y-m-d') }}</td>
                                        <td>
                                            @if($appointment['time_of_appointment'])
                                                {{ \Carbon\Carbon::parse($appointment['time_of_appointment'])->format('H:i') }}
                                            @else
                                                <span class="text-muted">{{ __('messages.not_specified') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">
                                                {{ number_format($appointment['price'], 2) }} {{ __('messages.currency') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($appointment['note'])
                                                <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $appointment['note'] }}">
                                                    {{ $appointment['note'] }}
                                                </span>
                                            @else
                                                <span class="text-muted">{{ __('messages.no_note') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($appointment['created_at'])->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            {{ __('messages.no_appointments_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Custom Pagination -->
                    @if($pagination['last_page'] > 1)
                        <div class="d-flex justify-content-center">
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    @if($pagination['current_page'] > 1)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}">
                                                {{ __('messages.previous') }}
                                            </a>
                                        </li>
                                    @endif

                                    @for($i = 1; $i <= $pagination['last_page']; $i++)
                                        @if($i == $pagination['current_page'])
                                            <li class="page-item active">
                                                <span class="page-link">{{ $i }}</span>
                                            </li>
                                        @elseif($i <= 3 || $i > $pagination['last_page'] - 3 || abs($i - $pagination['current_page']) <= 2)
                                            <li class="page-item">
                                                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                                            </li>
                                        @elseif($i == 4 && $pagination['current_page'] > 6)
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                        @elseif($i == $pagination['last_page'] - 3 && $pagination['current_page'] < $pagination['last_page'] - 5)
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                        @endif
                                    @endfor

                                    @if($pagination['current_page'] < $pagination['last_page'])
                                        <li class="page-item">
                                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}">
                                                {{ __('messages.next') }}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>

                        <div class="text-center mt-2">
                            <small class="text-muted">
                                {{ __('messages.showing') }} {{ $pagination['from'] }} {{ __('messages.to') }} {{ $pagination['to'] }} 
                                {{ __('messages.of') }} {{ $pagination['total'] }} {{ __('messages.results') }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const typeSelect = document.getElementById('type');
    const userSelect = document.getElementById('user_id');
    
    [typeSelect, userSelect].forEach(element => {
        if (element) {
            element.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        }
    });
});
</script>
@endsection