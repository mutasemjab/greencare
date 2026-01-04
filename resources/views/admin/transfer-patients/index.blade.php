@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('messages.transfer_patients_list') }}</h4>
                    @can('transfer-patient-add')
                    <a href="{{ route('transfer-patients.create') }}" class="btn btn-primary">
                        {{ __('messages.create_transfer') }}
                    </a>
                    @endcan
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('transfer-patients.index') }}" class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('messages.date_from') }}</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('messages.date_to') }}</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('messages.from_place') }}</label>
                                    <select name="from_place" class="form-control">
                                        <option value="">{{ __('messages.all') }}</option>
                                        <option value="1" {{ request('from_place') == '1' ? 'selected' : '' }}>{{ __('messages.inside_amman') }}</option>
                                        <option value="2" {{ request('from_place') == '2' ? 'selected' : '' }}>{{ __('messages.outside_amman') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('messages.to_place') }}</label>
                                    <select name="to_place" class="form-control">
                                        <option value="">{{ __('messages.all') }}</option>
                                        <option value="1" {{ request('to_place') == '1' ? 'selected' : '' }}>{{ __('messages.inside_amman') }}</option>
                                        <option value="2" {{ request('to_place') == '2' ? 'selected' : '' }}>{{ __('messages.outside_amman') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('messages.search') }}</label>
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('messages.search') }}" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">{{ __('messages.filter') }}</button>
                                    <a href="{{ route('transfer-patients.index') }}" class="btn btn-secondary">{{ __('messages.reset') }}</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    @can('transfer-patient-table')
                    @if($transfers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>{{ __('messages.id') }}</th>
                                        <th>{{ __('messages.user') }}</th>
                                        <th>{{ __('messages.date') }}</th>
                                        <th>{{ __('messages.time') }}</th>
                                        <th>{{ __('messages.from') }}</th>
                                        <th>{{ __('messages.to') }}</th>
                                        <th>{{ __('messages.note') }}</th>
                                        <th>{{ __('messages.created_at') }}</th>
                                        <th>{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transfers as $transfer)
                                        <tr>
                                            <td>{{ $transfer->id }}</td>
                                            <td>
                                                @if($transfer->user)
                                                    <div><strong>{{ $transfer->user->name }}</strong></div>
                                                    <small class="text-muted">{{ $transfer->user->email }}</small>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('messages.no_user') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $transfer->date_of_transfer->format('Y-m-d') }}</td>
                                            <td>{{ $transfer->time_of_transfer ? $transfer->time_of_transfer->format('H:i') : '-' }}</td>
                                            <td>
                                                <div><strong>{{ Str::limit($transfer->from_address, 30) }}</strong></div>
                                                <span class="badge {{ $transfer->from_place == 1 ? 'bg-success' : 'bg-info' }}">
                                                    {{ $transfer->from_place_text }}
                                                </span>
                                            </td>
                                            <td>
                                                <div><strong>{{ Str::limit($transfer->to_address, 30) }}</strong></div>
                                                <span class="badge {{ $transfer->to_place == 1 ? 'bg-success' : 'bg-info' }}">
                                                    {{ $transfer->to_place_text }}
                                                </span>
                                            </td>
                                            <td>{{ $transfer->note ? Str::limit($transfer->note, 50) : '-' }}</td>
                                            <td>{{ $transfer->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <div class="btn-group-vertical" role="group">
                                                    <a href="{{ route('transfer-patients.show', $transfer) }}" 
                                                       class="btn btn-info btn-sm mb-1">
                                                        {{ __('messages.view') }}
                                                    </a>
                                                    @can('transfer-patient-edit')
                                                    <a href="{{ route('transfer-patients.edit', $transfer) }}" 
                                                       class="btn btn-warning btn-sm mb-1">
                                                        {{ __('messages.edit') }}
                                                    </a>
                                                    @endcan
                                                    @can('transfer-patient-delete')
                                                    <form action="{{ route('transfer-patients.destroy', $transfer) }}" 
                                                          method="POST" 
                                                          style="display: inline-block;"
                                                          onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            {{ __('messages.delete') }}
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $transfers->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center">
                            <p class="text-muted">{{ __('messages.no_transfers_found') }}</p>
                            @can('transfer-patient-add')
                            <a href="{{ route('transfer-patients.create') }}" class="btn btn-primary">
                                {{ __('messages.create_first_transfer') }}
                            </a>
                            @endcan
                        </div>
                    @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection