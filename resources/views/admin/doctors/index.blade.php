@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.doctors') }}</h3>
                    @can('doctor-add')
                        <a href="{{ route('doctors.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.add_doctor') }}
                        </a>
                    @endcan
                </div>

                <div class="card-body">
         

                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('doctors.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="{{ __('messages.search_doctors') }}"
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
                                    <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary">
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
                                @forelse($doctors as $doctor)
                                    <tr>
                                        <td>{{ $loop->iteration + ($doctors->currentPage() - 1) * $doctors->perPage() }}</td>
                                        <td>
                                            @if($doctor->photo)
                                                <img src="{{ asset('assets/admin/uploads/' . $doctor->photo) }}" 
                                                     class="rounded-circle" 
                                                     width="40" height="40"
                                                     alt="{{ $doctor->name }}">
                                            @else
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" 
                                                     style="width: 40px; height: 40px;">
                                                    {{ substr($doctor->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $doctor->name }}</strong>
                                        </td>
                                        <td>{{ $doctor->phone }}</td>
                                        <td>{{ $doctor->email ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $doctor->gender == 1 ? 'info' : 'pink' }}">
                                                {{ $doctor->gender_text }}
                                            </span>
                                        </td>
                                        <td>{{ $doctor->date_of_birth }}</td>
                                        <td>
                                            <span class="badge bg-{{ $doctor->activate == 1 ? 'success' : 'danger' }}">
                                                {{ $doctor->active_status_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('doctor-table')
                                                    <a href="{{ route('doctors.show', $doctor) }}" 
                                                       class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('doctor-edit')
                                                    <a href="{{ route('doctors.edit', $doctor) }}" 
                                                       class="btn btn-sm btn-warning" title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('doctors.toggle-status', $doctor) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-{{ $doctor->activate == 1 ? 'secondary' : 'success' }}"
                                                                title="{{ $doctor->activate == 1 ? __('messages.deactivate') : __('messages.activate') }}"
                                                                onclick="return confirm('{{ __('messages.confirm_status_change') }}')">
                                                            <i class="fas fa-{{ $doctor->activate == 1 ? 'times' : 'check' }}"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('doctor-delete')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="{{ __('messages.delete') }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal{{ $doctor->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>

                                            @can('doctor-delete')
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal{{ $doctor->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('messages.confirm_delete') }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                {{ __('messages.are_you_sure_delete_doctor') }} "<strong>{{ $doctor->name }}</strong>"?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    {{ __('messages.cancel') }}
                                                                </button>
                                                                <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" class="d-inline">
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
                                                <i class="fas fa-doctor-injured fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">{{ __('messages.no_doctors_found') }}</p>
                                                @can('doctor-add')
                                                    <a href="{{ route('doctors.create') }}" class="btn btn-primary">
                                                        {{ __('messages.add_first_doctor') }}
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
                    @if($doctors->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $doctors->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

