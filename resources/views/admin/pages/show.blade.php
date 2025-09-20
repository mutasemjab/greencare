@extends('layouts.admin')

@section('title', __('messages.view_page'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.view_page') }}: {{ $page->title_en }}</h3>
                    <div class="btn-group">
                        <a href="{{ route('pages.edit', $page) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                        </a>
                        <a href="{{ route('pages.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Page Type -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-primary">
                                <h5 class="mb-0">
                                    <i class="fas fa-tag"></i> {{ __('messages.page_type') }}: 
                                    <span class="badge bg-{{ $page->type == 1 ? 'primary' : 'success' }} fs-6">
                                        {{ $page->type_name }}
                                    </span>
                                </h5>
                            </div>
                        </div>
                    </div>

                    <!-- Titles Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-globe"></i> {{ __('messages.english_version') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <h4 class="text-primary">{{ $page->title_en }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-language"></i> {{ __('messages.arabic_version') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <h4 class="text-secondary" style="{{ app()->getLocale() == 'ar' ? 'text-align: right;' : '' }}">{{ $page->title_ar }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descriptions Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-file-text"></i> {{ __('messages.description_en') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="description-content">
                                        {!! nl2br(e($page->description_en)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-file-text"></i> {{ __('messages.description_ar') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="description-content" style="{{ app()->getLocale() == 'ar' ? 'text-align: right;' : '' }}">
                                        {!! nl2br(e($page->description_ar)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page Information -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-dark">
                                <div class="card-header bg-dark text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle"></i> {{ __('messages.page_information') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>{{ __('messages.id') }}:</strong><br>
                                            <span class="badge bg-primary">{{ $page->id }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>{{ __('messages.type') }}:</strong><br>
                                            <span class="badge bg-{{ $page->type == 1 ? 'primary' : 'success' }}">
                                                {{ $page->type_name }}
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>{{ __('messages.created_at') }}:</strong><br>
                                            <small class="text-muted">{{ $page->created_at->format('Y-m-d H:i:s') }}</small>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>{{ __('messages.updated_at') }}:</strong><br>
                                            <small class="text-muted">{{ $page->updated_at->format('Y-m-d H:i:s') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('pages.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
                        </a>
                        <div class="btn-group">
                            <a href="{{ route('pages.edit', $page) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> {{ __('messages.edit_page') }}
                            </a>
                            <button type="button" 
                                    class="btn btn-danger" 
                                    onclick="confirmDelete({{ $page->id }}, '{{ addslashes($page->title_en) }}')">
                                <i class="fas fa-trash"></i> {{ __('messages.delete_page') }}
                            </button>
                        </div>
                    </div>

                    <!-- Hidden delete form -->
                    <form id="delete-form-{{ $page->id }}" 
                          action="{{ route('pages.destroy', $page) }}" 
                          method="POST" 
                          style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
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