@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.families') }}</h3>
                    <a href="{{ route('families.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.add_family') }}
                    </a>
                </div>

                <div class="card-body">
                 

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.family_name') }}</th>
                                    <th>{{ __('messages.members_count') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($families as $family)
                                    <tr>
                                        <td>{{ $loop->iteration + ($families->currentPage() - 1) * $families->perPage() }}</td>
                                        <td>
                                            <strong>{{ $family->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $family->users_count }} {{ __('messages.members') }}</span>
                                        </td>
                                        <td>{{ $family->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('families.show', $family) }}" 
                                                   class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('families.edit', $family) }}" 
                                                   class="btn btn-sm btn-warning" title="{{ __('messages.edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="{{ __('messages.delete') }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal{{ $family->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal{{ $family->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('messages.confirm_delete') }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            {{ __('messages.are_you_sure_delete_family') }} "<strong>{{ $family->name }}</strong>"?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                {{ __('messages.cancel') }}
                                                            </button>
                                                            <form action="{{ route('families.destroy', $family) }}" method="POST" class="d-inline">
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
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">{{ __('messages.no_families_found') }}</p>
                                                <a href="{{ route('families.create') }}" class="btn btn-primary">
                                                    {{ __('messages.add_first_family') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($families->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $families->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


