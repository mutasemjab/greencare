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
                        <li class="breadcrumb-item"><a href="{{ route('admin.role.index') }}">Roles</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">Create New Role</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Create New Role</h3>
                    <a href="{{ route('admin.role.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.role.store') }}">
                        @csrf

                        <!-- Role Name -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}"
                                       placeholder="Enter role name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Permissions Section -->
                        <h4 class="mb-3">Permissions</h4>

                        @foreach($groupedPermissions as $resource => $permissions)
                            @if(count($permissions) > 0)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 text-capitalize">{{ ucfirst($resource) }}</h5>
                                        <button type="button" class="btn btn-sm btn-outline-primary check-all-btn" data-resource="{{ $resource }}">
                                            <i class="fas fa-check-square"></i> Check All
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <tbody>
                                                @foreach($permissions as $permission)
                                                <tr>
                                                    <td width="50">
                                                        <input type="checkbox" name="perms[]"
                                                               value="{{ $permission->id }}"
                                                               id="perm_{{ $permission->id }}"
                                                               class="resource-{{ $resource }}"
                                                               {{ in_array($permission->id, old('perms', [])) ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <label for="perm_{{ $permission->id }}" class="mb-0">
                                                            <strong>{{ $permission->name }}</strong>
                                                        </label>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach

                        @error('perms')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save
                                </button>
                                <a href="{{ route('admin.role.index') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.check-all-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const resource = this.dataset.resource;
            const checkboxes = document.querySelectorAll(`.resource-${resource}`);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });

            this.innerHTML = !allChecked ?
                '<i class="fas fa-square"></i> Uncheck All' :
                '<i class="fas fa-check-square"></i> Check All';
        });
    });
});
</script>
@endpush

@endsection
