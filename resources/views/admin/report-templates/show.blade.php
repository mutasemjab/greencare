@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.report_template') }}: {{ $reportTemplate->title }}</h3>
                        <div class="btn-group">
                            @can('report-template-edit')
                                <a href="{{ route('report-templates.edit', $reportTemplate) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                                <form action="{{ route('report-templates.duplicate', $reportTemplate) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-secondary"
                                            onclick="return confirm('{{ __('messages.confirm_duplicate_template') }}')"
                                            title="{{ __('messages.duplicate') }}">
                                        <i class="fas fa-copy"></i> {{ __('messages.duplicate') }}
                                    </button>
                                </form>
                            @endcan
                            <a href="{{ route('report-templates.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Template Information -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.template_information') }}</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.title_en') }}:</strong></td>
                                            <td>{{ $reportTemplate->title_en }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.title_ar') }}:</strong></td>
                                            <td>{{ $reportTemplate->title_ar }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.created_for') }}:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $reportTemplate->created_for == 'doctor' ? 'primary' : 'success' }}">
                                                    {{ $reportTemplate->created_for_text }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.sections_count') }}:</strong></td>
                                            <td>
                                                <span class="badge badge-info">{{ $reportTemplate->sections->count() }} {{ __('messages.sections') }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.created_at') }}:</strong></td>
                                            <td>{{ $reportTemplate->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.updated_at') }}:</strong></td>
                                            <td>{{ $reportTemplate->updated_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('messages.template_stats') }}</h5>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h3 class="text-primary">{{ $reportTemplate->sections->count() }}</h3>
                                            <small class="text-muted">{{ __('messages.sections') }}</small>
                                        </div>
                                        <div class="col-6">
                                            <h3 class="text-success">{{ $reportTemplate->sections->sum(function($section) { return $section->fields->count(); }) }}</h3>
                                            <small class="text-muted">{{ __('messages.total_fields') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Template Preview -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ __('messages.template_preview') }}</h5>
                                </div>
                                <div class="card-body">
                                    @if($reportTemplate->sections->count() > 0)
                                        @foreach($reportTemplate->sections as $section)
                                            <div class="section-preview mb-4">
                                                <div class="section-header bg-primary text-white p-3 rounded-top">
                                                    <h5 class="mb-0">
                                                        <i class="fas fa-folder-open mr-2"></i>
                                                        {{ $section->title }}
                                                        <small class="float-right">
                                                            {{ $section->fields->count() }} {{ __('messages.fields') }}
                                                        </small>
                                                    </h5>
                                                </div>
                                                
                                                <div class="section-content border border-top-0 p-3">
                                                    @if($section->fields->count() > 0)
                                                        <div class="row">
                                                            @foreach($section->fields as $field)
                                                                <div class="col-md-6 mb-3">
                                                                    <div class="field-preview border rounded p-3 bg-light">
                                                                        <div class="field-header mb-2">
                                                                            <strong>{{ $field->label }}</strong>
                                                                            @if($field->required)
                                                                                <span class="text-danger">*</span>
                                                                            @endif
                                                                            <span class="badge badge-secondary badge-sm ml-2">{{ $field->input_type_text }}</span>
                                                                        </div>
                                                                        
                                                                        <div class="field-demo">
                                                                            @switch($field->input_type)
                                                                                @case('text')
                                                                                    <input type="text" class="form-control" placeholder="{{ $field->label }}" disabled>
                                                                                    @break
                                                                                
                                                                                @case('textarea')
                                                                                    <textarea class="form-control" rows="3" placeholder="{{ $field->label }}" disabled></textarea>
                                                                                    @break
                                                                                
                                                                                @case('number')
                                                                                    <input type="number" class="form-control" placeholder="{{ $field->label }}" disabled>
                                                                                    @break
                                                                                
                                                                                @case('date')
                                                                                    <input type="date" class="form-control" disabled>
                                                                                    @break
                                                                                
                                                                                @case('select')
                                                                                    <select class="form-control" disabled>
                                                                                        <option>{{ __('messages.select_option') }}</option>
                                                                                        @foreach($field->options as $option)
                                                                                            <option>{{ $option->value }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                    @break
                                                                                
                                                                                @case('radio')
                                                                                    @foreach($field->options as $option)
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" type="radio" disabled>
                                                                                            <label class="form-check-label">{{ $option->value }}</label>
                                                                                        </div>
                                                                                    @endforeach
                                                                                    @break
                                                                                
                                                                                @case('checkbox')
                                                                                    @foreach($field->options as $option)
                                                                                        <div class="form-check">
                                                                                            <input class="form-check-input" type="checkbox" disabled>
                                                                                            <label class="form-check-label">{{ $option->value }}</label>
                                                                                        </div>
                                                                                    @endforeach
                                                                                    @break
                                                                                
                                                                                @case('boolean')
                                                                                    <div class="form-check form-check-inline">
                                                                                        <input class="form-check-input" type="radio" disabled>
                                                                                        <label class="form-check-label">{{ __('messages.yes') }}</label>
                                                                                    </div>
                                                                                    <div class="form-check form-check-inline">
                                                                                        <input class="form-check-input" type="radio" disabled>
                                                                                        <label class="form-check-label">{{ __('messages.no') }}</label>
                                                                                    </div>
                                                                                    @break
                                                                                
                                                                                @case('gender')
                                                                                    <div class="form-check form-check-inline">
                                                                                        <input class="form-check-input" type="radio" disabled>
                                                                                        <label class="form-check-label">{{ __('messages.male') }}</label>
                                                                                    </div>
                                                                                    <div class="form-check form-check-inline">
                                                                                        <input class="form-check-input" type="radio" disabled>
                                                                                        <label class="form-check-label">{{ __('messages.female') }}</label>
                                                                                    </div>
                                                                                    @break
                                                                                
                                                                                @default
                                                                                    <input type="text" class="form-control" placeholder="{{ $field->label }}" disabled>
                                                                            @endswitch
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="text-center text-muted py-3">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            {{ __('messages.no_fields_in_section') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-5">
                                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">{{ __('messages.no_sections_in_template') }}</p>
                                            @can('report-template-edit')
                                                <a href="{{ route('report-templates.edit', $reportTemplate) }}" class="btn btn-primary">
                                                    {{ __('messages.add_sections') }}
                                                </a>
                                            @endcan
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
$(document).ready(function() {
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush