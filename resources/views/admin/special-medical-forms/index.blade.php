@extends('layouts.admin')

@section('title', __('messages.special_medical_forms'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.special_medical_forms') }}</h3>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('special-medical-forms.index') }}">
                                <select name="room_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">{{ __('messages.all_rooms') }}</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                                            {{ $room->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="status" value="{{ request('status') }}">
                                <input type="hidden" name="creator_type" value="{{ request('creator_type') }}">
                            </form>
                        </div>
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('special-medical-forms.index') }}">
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">{{ __('messages.all_statuses') }}</option>
                                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>
                                        {{ __('messages.form_status_open') }}
                                    </option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>
                                        {{ __('messages.form_status_closed') }}
                                    </option>
                                </select>
                                <input type="hidden" name="room_id" value="{{ request('room_id') }}">
                                <input type="hidden" name="creator_type" value="{{ request('creator_type') }}">
                            </form>
                        </div>
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('special-medical-forms.index') }}">
                                <select name="creator_type" class="form-control" onchange="this.form.submit()">
                                    <option value="">{{ __('messages.all_creator_types') }}</option>
                                    <option value="doctor" {{ request('creator_type') == 'doctor' ? 'selected' : '' }}>
                                        {{ __('messages.doctor') }}
                                    </option>
                                    <option value="nurse" {{ request('creator_type') == 'nurse' ? 'selected' : '' }}>
                                        {{ __('messages.nurse') }}
                                    </option>
                                </select>
                                <input type="hidden" name="room_id" value="{{ request('room_id') }}">
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            </form>
                        </div>
                    </div>

                    @if(request()->hasAny(['room_id', 'status', 'creator_type']))
                        <div class="mb-3">
                            <a href="{{ route('special-medical-forms.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times"></i> {{ __('messages.clear_filters') }}
                            </a>
                        </div>
                    @endif

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.id') }}</th>
                                    <th>{{ __('messages.room') }}</th>
                                    <th>{{ __('messages.title') }}</th>
                                    <th>{{ __('messages.created_by') }}</th>
                                    <th>{{ __('messages.user_type') }}</th>
                                    <th>{{ __('messages.replies_count') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($forms as $form)
                                    <tr>
                                        <td>{{ $form->id }}</td>
                                        <td>
                                            <strong>{{ $form->room->title }}</strong>
                                            @if($form->room->description)
                                                <br><small class="text-muted">{{ Str::limit($form->room->description, 30) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $form->title }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($form->creator->photo)
                                                    <img src="{{ asset('storage/' . $form->creator->photo) }}" 
                                                         alt="{{ $form->creator->name }}" 
                                                         class="rounded-circle me-2" 
                                                         width="30" 
                                                         height="30">
                                                @endif
                                                <span>{{ $form->creator->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($form->creator->user_type == 'doctor')
                                                <span class="badge bg-primary">{{ __('messages.doctor') }}</span>
                                            @elseif($form->creator->user_type == 'nurse')
                                                <span class="badge bg-info">{{ __('messages.nurse') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $form->replies_count }} {{ __('messages.replies') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($form->status == 'open')
                                                <span class="badge bg-success">{{ __('messages.form_status_open') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('messages.form_status_closed') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $form->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('special-medical-forms.show', $form) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> {{ __('messages.view') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            {{ __('messages.no_data_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $forms->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection