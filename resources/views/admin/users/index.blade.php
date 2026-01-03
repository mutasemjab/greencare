@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.patients') }}</h3>
                    @can('user-add')
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.add_patient') }}
                        </a>
                    @endcan
                </div>

                <div class="card-body">
         

                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('users.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="{{ __('messages.search_patients') }}"
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="gender" class="form-control">
                                    <option value="">{{ __('messages.all_genders') }}</option>
                                    <option value="1" {{ request('gender') == '1' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                                    <option value="2" {{ request('gender') == '2' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="activate" class="form-control">
                                    <option value="">{{ __('messages.all_status') }}</option>
                                    <option value="1" {{ request('activate') == '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                    <option value="2" {{ request('activate') == '2' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> {{ __('messages.search') }}
                                    </button>
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> {{ __('messages.clear') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.photo') }}</th>
                                    <th>{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.phone') }}</th>
                                    <th>{{ __('messages.email') }}</th>
                                    <th>{{ __('messages.gender') }}</th>
                                    <th>{{ __('messages.date_of_birth') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                        <td>
                                            @if($user->photo)
                                                <img src="{{ asset('assets/admin/uploads/' . $user->photo) }}" 
                                                     class="rounded-circle" 
                                                     width="40" height="40"
                                                     alt="{{ $user->name }}">
                                            @else
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" 
                                                     style="width: 40px; height: 40px;">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $user->name }}</strong>
                                        </td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->email ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->gender == 1 ? 'info' : 'pink' }}">
                                                {{ $user->gender_text }}
                                            </span>
                                        </td>
                                        <td>{{ $user->date_of_birth }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->activate == 1 ? 'success' : 'danger' }}">
                                                {{ $user->active_status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                              
                                                @can('user-edit')
                                                    <a href="{{ route('users.edit', $user) }}" 
                                                       class="btn btn-sm btn-warning" title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-{{ $user->activate == 1 ? 'secondary' : 'success' }}"
                                                                title="{{ $user->activate == 1 ? __('messages.deactivate') : __('messages.activate') }}"
                                                                onclick="return confirm('{{ __('messages.confirm_status_change') }}')">
                                                            <i class="fas fa-{{ $user->activate == 1 ? 'times' : 'check' }}"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('user-delete')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="{{ __('messages.delete') }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal{{ $user->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>

                                            @can('user-delete')
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('messages.confirm_delete') }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                {{ __('messages.are_you_sure_delete_patient') }} "<strong>{{ $user->name }}</strong>"?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    {{ __('messages.cancel') }}
                                                                </button>
                                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
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
                                                <i class="fas fa-user-injured fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">{{ __('messages.no_patients_found') }}</p>
                                                @can('user-add')
                                                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                                                        {{ __('messages.add_first_patient') }}
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
                    @if($users->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $users->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

