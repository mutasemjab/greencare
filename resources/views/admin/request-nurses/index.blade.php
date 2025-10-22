@extends('layouts.admin')

@section('title', __('messages.request_nurses_types'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.request_nurses_types') }}</h3>
                    <a href="{{ route('request-nurses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.add_new') }}
                    </a>
                </div>

                <div class="card-body">
                   

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('request-nurses.index') }}" class="d-flex">
                                <select name="type_of_service" class="form-control me-2" onchange="this.form.submit()">
                                    <option value="">{{ __('messages.all_service_types') }}</option>
                                    @foreach(\App\Models\TypeRequestNurse::getServiceTypes() as $key => $value)
                                        <option value="{{ $key }}" {{ request('type_of_service') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                            </form>
                        </div>
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('request-nurses.index') }}" class="d-flex">
                                <input type="number" 
                                       name="min_price" 
                                       class="form-control me-2" 
                                       placeholder="{{ __('messages.min_price') }}" 
                                       value="{{ request('min_price') }}"
                                       step="0.01"
                                       min="0">
                                <input type="number" 
                                       name="max_price" 
                                       class="form-control me-2" 
                                       placeholder="{{ __('messages.max_price') }}" 
                                       value="{{ request('max_price') }}"
                                       step="0.01"
                                       min="0">
                                <button type="submit" class="btn btn-secondary me-2">{{ __('messages.filter') }}</button>
                                <a href="{{ route('request-nurses.index') }}" class="btn btn-outline-secondary">{{ __('messages.clear') }}</a>
                                <input type="hidden" name="type_of_service" value="{{ request('type_of_service') }}">
                            </form>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.id') }}</th>
                                    <th>{{ __('messages.service_type') }}</th>
                                    <th>{{ __('messages.price') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requestNurses as $care)
                                    <tr>
                                        <td>{{ $care->id }}</td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $care->translated_service_type }}
                                            </span>
                                        </td>
                                        <td>{{ $care->formatted_price }}</td>
                                        <td>{{ $care->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('request-nurses.show', $care) }}" 
                                                   class="btn btn-sm btn-info">
                                                    {{ __('messages.view') }}
                                                </a>
                                                <a href="{{ route('request-nurses.edit', $care) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    {{ __('messages.edit') }}
                                                </a>
                                                <form action="{{ route('request-nurses.destroy', $care) }}" 
                                                      method="POST" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        {{ __('messages.delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            {{ __('messages.no_data_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $requestNurses->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection