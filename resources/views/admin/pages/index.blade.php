@extends('layouts.admin')

@section('title', __('messages.pages_management'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.pages_management') }}</h3>
                    <a href="{{ route('pages.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.add_new_page') }}
                    </a>
                </div>


                <div class="card-body">
                    @if($pages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>{{ __('messages.id') }}</th>
                                        <th>{{ __('messages.type') }}</th>
                                        <th>{{ __('messages.title_en') }}</th>
                                        <th>{{ __('messages.title_ar') }}</th>
                                        <th>{{ __('messages.created_at') }}</th>
                                        <th>{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pages as $page)
                                        <tr>
                                            <td>{{ $page->id }}</td>
                                            <td>
                                                <span class="badge bg-{{ $page->type == 1 ? 'primary' : 'success' }}">
                                                    {{ $page->type_name }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ Str::limit($page->title_en, 30) }}</strong>
                                            </td>
                                            <td>
                                                <strong>{{ Str::limit($page->title_ar, 30) }}</strong>
                                            </td>
                                            <td>{{ $page->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('pages.show', $page) }}" 
                                                       class="btn btn-sm btn-info" 
                                                       title="{{ __('messages.view') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('pages.edit', $page) }}" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="{{ __('messages.delete') }}"
                                                            onclick="confirmDelete({{ $page->id }}, '{{ addslashes($page->title_en) }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Hidden delete form -->
                                                <form id="delete-form-{{ $page->id }}" 
                                                      action="{{ route('pages.destroy', $page) }}" 
                                                      method="POST" 
                                                      style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">{{ __('messages.no_pages_found') }}</h4>
                            <p class="text-muted">{{ __('messages.create_first_page') }}</p>
                            <a href="{{ route('pages.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('messages.add_new_page') }}
                            </a>
                        </div>
                    @endif
                </div>

                @if($pages->count() > 0)
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <div class="dataTables_info">
                                    {{ __('messages.showing_entries', [
                                        'start' => 1,
                                        'end' => $pages->count(),
                                        'total' => $pages->count()
                                    ]) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Delete confirmation modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.confirm_deletion') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('messages.are_you_sure_delete_page') }}</p>
                <p><strong id="page-title"></strong></p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ __('messages.this_action_cannot_be_undone') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('messages.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteConfirmed()">
                    <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentDeleteId = null;

function confirmDelete(id, title) {
    currentDeleteId = id;
    document.getElementById('page-title').textContent = title;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function deleteConfirmed() {
    if (currentDeleteId) {
        document.getElementById('delete-form-' + currentDeleteId).submit();
    }
}
</script>


@endsection