@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.report_templates') }}</h3>
                    @can('report-template-add')
                        <a href="{{ route('report-templates.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.add_report_template') }}
                        </a>
                    @endcan
                </div>

                <div class="card-body">
                

                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('report-templates.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="{{ __('messages.search_templates') }}"
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="created_for" class="form-control">
                                    <option value="">{{ __('messages.all_types') }}</option>
                                    <option value="doctor" {{ request('created_for') == 'doctor' ? 'selected' : '' }}>{{ __('messages.doctor') }}</option>
                                    <option value="nurse" {{ request('created_for') == 'nurse' ? 'selected' : '' }}>{{ __('messages.nurse') }}</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> {{ __('messages.search') }}
                                    </button>
                                    <a href="{{ route('report-templates.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> {{ __('messages.clear') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.title_en') }}</th>
                                    <th>{{ __('messages.title_ar') }}</th>
                                    <th>{{ __('messages.created_for') }}</th>
                                    <th>{{ __('messages.sections_count') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates as $template)
                                    <tr>
                                        <td>{{ $loop->iteration + ($templates->currentPage() - 1) * $templates->perPage() }}</td>
                                        <td>
                                            <strong>{{ $template->title_en }}</strong>
                                        </td>
                                        <td>{{ $template->title_ar }}</td>
                                        <td>
                                            <span class="badge badge-{{ $template->created_for == 'doctor' ? 'primary' : 'success' }}">
                                                {{ $template->created_for_text }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $template->sections->count() }} {{ __('messages.sections') }}</span>
                                        </td>
                                        <td>{{ $template->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('report-template-table')
                                                    <a href="{{ route('report-templates.show', $template) }}" 
                                                       class="btn btn-sm btn-info" title="{{ __('messages.view') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('report-template-edit')
                                                    <a href="{{ route('report-templates.edit', $template) }}" 
                                                       class="btn btn-sm btn-warning" title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('report-templates.duplicate', $template) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-secondary"
                                                                title="{{ __('messages.duplicate') }}"
                                                                onclick="return confirm('{{ __('messages.confirm_duplicate_template') }}')">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('report-template-delete')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="{{ __('messages.delete') }}"
                                                            data-toggle="modal" 
                                                            data-target="#deleteModal{{ $template->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>

                                            @can('report-template-delete')
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal{{ $template->id }}" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('messages.confirm_delete') }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                {{ __('messages.are_you_sure_delete_template') }} "<strong>{{ $template->title_en }}</strong>"?
                                                                <br><small class="text-muted">{{ __('messages.delete_template_warning') }}</small>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                    {{ __('messages.cancel') }}
                                                                </button>
                                                                <form action="{{ route('report-templates.destroy', $template) }}" method="POST" class="d-inline">
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
                                        <td colspan="7" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">{{ __('messages.no_templates_found') }}</p>
                                                @can('report-template-add')
                                                    <a href="{{ route('report-templates.create') }}" class="btn btn-primary">
                                                        {{ __('messages.add_first_template') }}
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
                    @if($templates->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $templates->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush

