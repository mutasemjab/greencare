@extends("layouts.admin")

@section('css')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ env('APP_NAME') }}</a></li>
                        <li class="breadcrumb-item active">Roles</li>
                    </ol>
                </div>
                <h4 class="page-title">Roles</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Roles</h3>
                    <a href="{{ route('admin.role.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Role
                    </a>
                </div>

                <div class="card-body">
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('admin.role.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control"
                                       placeholder="Search role name"
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info btn-sm">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.role.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Roles Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Permissions</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $role)
                                    <tr>
                                        <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $role->name }}</strong>
                                        </td>
                                        <td>
                                            <div style="font-size: 11px; max-width: 400px;">
                                                @foreach($role->permissions as $permission)
                                                    <span class="badge bg-secondary me-1 mb-1">{{ $permission->name }}</span>
                                                @endforeach
                                                @if($role->permissions->isEmpty())
                                                    <span class="text-muted">No permissions</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.role.edit', $role->id) }}"
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.role.delete') }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $role->id }}">
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this role?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No roles found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $data->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.3/dist/sweetalert2.min.js"></script>
@endsection
