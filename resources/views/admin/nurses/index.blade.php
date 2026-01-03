@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.nurses') }}</h3>
                    @can('nurse-add')
                        <a href="{{ route('nurses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.add_nurse') }}
                        </a>
                    @endcan
                </div>

                <div class="card-body">
         

                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('nurses.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="{{ __('messages.search_nurses') }}"
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
                                    <a href="{{ route('nurses.index') }}" class="btn btn-outline-secondary">
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
                                @forelse($nurses as $nurse)
                                    <tr>
                                        <td>{{ $loop->iteration + ($nurses->currentPage() - 1) * $nurses->perPage() }}</td>
                                        <td>
                                            @if($nurse->photo)
                                                <img src="{{ asset('assets/admin/uploads/' . $nurse->photo) }}" 
                                                     class="rounded-circle" 
                                                     width="40" height="40"
                                                     alt="{{ $nurse->name }}">
                                            @else
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" 
                                                     style="width: 40px; height: 40px;">
                                                    {{ substr($nurse->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $nurse->name }}</strong>
                                        </td>
                                        <td>{{ $nurse->phone }}</td>
                                        <td>{{ $nurse->email ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $nurse->gender == 1 ? 'info' : 'pink' }}">
                                                {{ $nurse->gender_text }}
                                            </span>
                                        </td>
                                        <td>{{ $nurse->date_of_birth }}</td>
                                        <td>
                                            <span class="badge bg-{{ $nurse->activate == 1 ? 'success' : 'danger' }}">
                                                {{ $nurse->active_status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                               
                                                @can('nurse-edit')
                                                    <a href="{{ route('nurses.edit', $nurse) }}" 
                                                       class="btn btn-sm btn-warning" title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('nurses.toggle-status', $nurse) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-{{ $nurse->activate == 1 ? 'secondary' : 'success' }}"
                                                                title="{{ $nurse->activate == 1 ? __('messages.deactivate') : __('messages.activate') }}"
                                                                onclick="return confirm('{{ __('messages.confirm_status_change') }}')">
                                                            <i class="fas fa-{{ $nurse->activate == 1 ? 'times' : 'check' }}"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('nurse-delete')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="{{ __('messages.delete') }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal{{ $nurse->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>

                                            @can('nurse-delete')
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal{{ $nurse->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('messages.confirm_delete') }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                {{ __('messages.are_you_sure_delete_nurse') }} "<strong>{{ $nurse->name }}</strong>"?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    {{ __('messages.cancel') }}
                                                                </button>
                                                                <form action="{{ route('nurses.destroy', $nurse) }}" method="POST" class="d-inline">
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
                                                <i class="fas fa-nurse-injured fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">{{ __('messages.no_nurses_found') }}</p>
                                                @can('nurse-add')
                                                    <a href="{{ route('nurses.create') }}" class="btn btn-primary">
                                                        {{ __('messages.add_first_nurse') }}
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
                    @if($nurses->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $nurses->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

