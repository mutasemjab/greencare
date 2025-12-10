@extends('layouts.admin')

@section('title', __('messages.shower_appointments'))

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-shower me-2"></i>
            {{ __('messages.shower_appointments') }}
        </h2>
        <a href="{{ route('showers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            {{ __('messages.new_appointment') }}
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">{{ __('messages.total_appointments') }}</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">{{ __('messages.today_appointments') }}</h6>
                            <h3 class="mb-0">{{ $stats['today'] }}</h3>
                        </div>
                        <i class="fas fa-calendar-day fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">{{ __('messages.this_month_appointments') }}</h6>
                            <h3 class="mb-0">{{ $stats['this_month'] }}</h3>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">{{ __('messages.total_revenue') }}</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_revenue'], 2) }}</h3>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Method Stats -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('messages.card_payments') }}</h6>
                            <h4 class="mb-0 text-primary">{{ $stats['card_payments'] }}</h4>
                        </div>
                        <i class="fas fa-credit-card fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">{{ __('messages.cash_payments') }}</h6>
                            <h4 class="mb-0 text-success">{{ $stats['cash_payments'] }}</h4>
                        </div>
                        <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('showers.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.from_date') }}</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.to_date') }}</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.user') }}</label>
                        <select name="user_id" class="form-control">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.payment_method') }}</label>
                        <select name="payment_method" class="form-control">
                            <option value="">{{ __('messages.all') }}</option>
                            <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>
                                {{ __('messages.card') }}
                            </option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>
                                {{ __('messages.cash') }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">{{ __('messages.patient_code') }}</label>
                        <input type="text" name="code_patient" class="form-control" 
                               placeholder="{{ __('messages.search_by_patient_code') }}" 
                               value="{{ request('code_patient') }}">
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>{{ __('messages.search') }}
                        </button>
                        <a href="{{ route('showers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>{{ __('messages.reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('messages.id') }}</th>
                            <th>{{ __('messages.user') }}</th>
                            <th>{{ __('messages.patient_code') }}</th>
                            <th>{{ __('messages.date_and_time') }}</th>
                            <th>{{ __('messages.price') }}</th>
                            <th>{{ __('messages.payment_method') }}</th>
                            <th>{{ __('messages.notes') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($showers as $shower)
                        <tr>
                            <td>{{ $shower->id }}</td>
                            <td>
                                <div>
                                    <strong>{{ $shower->user->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $shower->user->email }}</small>
                                </div>
                            </td>
                            <td>
                                @if($shower->code_patient)
                                    <span class="badge bg-info">{{ $shower->code_patient }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $shower->date_of_shower->format('Y-m-d') }}
                                </div>
                                @if($shower->time_of_shower)
                                    <div>
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $shower->time_of_shower->format('H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong class="text-success">{{ number_format($shower->price, 2) }} {{ __('messages.currency') }}</strong>
                            </td>
                            <td>
                                @if($shower->card_number_id)
                                    <span class="badge bg-primary">
                                        <i class="fas fa-credit-card me-1"></i>
                                        {{ __('messages.card') }}
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        {{ $shower->cardNumber->number ?? '' }}
                                    </small>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-money-bill-wave me-1"></i>
                                        {{ __('messages.cash') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($shower->note)
                                    <small>{{ Str::limit($shower->note, 30) }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('showers.show', $shower->id) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="{{ __('messages.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('showers.edit', $shower->id) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="{{ __('messages.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('showers.destroy', $shower->id) }}" 
                                          method="POST" 
                                          class="d-inline" 
                                          onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-danger" 
                                                title="{{ __('messages.delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">{{ __('messages.no_appointments') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $showers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

