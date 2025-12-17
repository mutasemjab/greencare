@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.super_nurses') }}</h3>
                    @can('super-nurse-add')
                        <a href="{{ route('super-nurses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.add_super_nurse') }}
                        </a>
                    @endcan
                </div>

                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('super-nurses.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="{{ __('messages.search_super_nurses') }}"
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
                                    <a href="{{ route('super-nurses.index') }}" class="btn btn-outline-secondary">
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
                                @forelse($superNurses as $superNurse)
                                    <tr>
                                        <td>{{ $loop->iteration + ($superNurses->currentPage() - 1) * $superNurses->perPage() }}</td>
                                        <td>
                                            @if($superNurse->photo)
                                                <img src="{{ asset('assets/admin/uploads/' . $superNurse->photo) }}" 
                                                     class="rounded-circle" 
                                                     width="40" height="40"
                                                     alt="{{ $superNurse->name }}">
                                            @else
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger text-white" 
                                                     style="width: 40px; height: 40px;">
                                                    {{ substr($superNurse->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $superNurse->name }}</strong>
                                            <br><span class="badge bg-danger">{{ __('messages.super_nurse') }}</span>
                                        </td>
                                        <td>{{ $superNurse->phone }}</td>
                                        <td>{{ $superNurse->email ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $superNurse->gender == 1 ? 'info' : 'pink' }}">
                                                {{ $superNurse->gender_text }}
                                            </span>
                                        </td>
                                        <td>{{ $superNurse->date_of_birth }}</td>
                                        <td>
                                            <span class="badge bg-{{ $superNurse->activate == 1 ? 'success' : 'danger' }}">
                                                {{ $superNurse->active_status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('super-nurse-table')
                                                    <a href="{{ route('super-nurses.show', $superNurse) }}" 
                                                       class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('super-nurse-edit')
                                                    <a href="{{ route('super-nurses.edit', $superNurse) }}" 
                                                       class="btn btn-sm btn-warning" title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('super-nurses.toggle-status', $superNurse) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-{{ $superNurse->activate == 1 ? 'secondary' : 'success' }}"
                                                                title="{{ $superNurse->activate == 1 ? __('messages.deactivate') : __('messages.activate') }}"
                                                                onclick="return confirm('{{ __('messages.confirm_status_change') }}')">
                                                            <i class="fas fa-{{ $superNurse->activate == 1 ? 'times' : 'check' }}"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('super-nurse-delete')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="{{ __('messages.delete') }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal{{ $superNurse->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>

                                            @can('super-nurse-delete')
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal{{ $superNurse->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('messages.confirm_delete') }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                {{ __('messages.are_you_sure_delete_super_nurse') }} "<strong>{{ $superNurse->name }}</strong>"?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    {{ __('messages.cancel') }}
                                                                </button>
                                                                <form action="{{ route('super-nurses.destroy', $superNurse) }}" method="POST" class="d-inline">
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
                                                <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">{{ __('messages.no_super_nurses_found') }}</p>
                                                @can('super-nurse-add')
                                                    <a href="{{ route('super-nurses.create') }}" class="btn btn-primary">
                                                        {{ __('messages.add_first_super_nurse') }}
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
                    @if($superNurses->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $superNurses->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection