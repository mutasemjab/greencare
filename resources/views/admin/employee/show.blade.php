@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.employee_details') }}</h3>
                        <div>
                            @can('employee-edit')
                                <a href="{{ route('admin.employee.edit', $admin->id) }}" class="btn btn-warning btn-sm me-2">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('admin.employee.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Employee Information -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user-tie"></i> {{ __('messages.employee_information') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.name') }}:</strong>
                                            <p>{{ $admin->name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.username') }}:</strong>
                                            <p>{{ $admin->username ?? '-' }}</p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.email') }}:</strong>
                                            <p>
                                                @if($admin->email)
                                                    <a href="mailto:{{ $admin->email }}">{{ $admin->email }}</a>
                                                @else
                                                    <span class="text-muted">{{ __('messages.not_available') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.phone') }}:</strong>
                                            <p>
                                                @if($admin->phone)
                                                    <a href="tel:{{ $admin->phone }}">{{ $admin->phone }}</a>
                                                @else
                                                    <span class="text-muted">{{ __('messages.not_available') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.created_at') }}:</strong>
                                            <p>{{ $admin->created_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>{{ __('messages.updated_at') }}:</strong>
                                            <p>{{ $admin->updated_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Roles -->
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-shield-alt"></i> {{ __('messages.roles') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($roles && $roles->count() > 0)
                                        <div class="list-group">
                                            @foreach($roles as $role)
                                                <span class="badge bg-primary me-1 mb-1">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">{{ __('messages.no_roles_assigned') }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="card mt-3">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-exclamation-triangle"></i> {{ __('messages.actions') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @can('employee-delete')
                                        <form action="{{ route('admin.employee.destroy', $admin->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm btn-block" onclick="return confirm('{{ __('messages.are_you_sure') }}');">
                                                <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                                            </button>
                                        </form>
                                    @endcan
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
