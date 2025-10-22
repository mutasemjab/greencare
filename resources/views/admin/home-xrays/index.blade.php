@extends('layouts.admin')

@section('title', __('messages.home_xray_types'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.home_xray_types') }}</h3>
                    <a href="{{ route('home-xrays.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.add_new') }}
                    </a>
                </div>

                <div class="card-body">

                    <!-- Filters -->
                    <div class="row mb-3">
                        <!-- Search Filter -->
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('home-xrays.index') }}" class="d-flex">
                                <input type="text" 
                                       name="search" 
                                       class="form-control me-2" 
                                       placeholder="{{ __('messages.search_by_name') }}" 
                                       value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">{{ __('messages.search') }}</button>
                                <!-- Hidden fields to preserve other filters -->
                                <input type="hidden" name="category_type" value="{{ request('category_type') }}">
                                <input type="hidden" name="parent_id" value="{{ request('parent_id') }}">
                                <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                            </form>
                        </div>

                        <!-- Category Type Filter -->
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('home-xrays.index') }}" id="categoryTypeForm">
                                <select name="category_type" class="form-control" onchange="document.getElementById('categoryTypeForm').submit();">
                                    <option value="">{{ __('messages.all_categories') }}</option>
                                    <option value="main" {{ request('category_type') === 'main' ? 'selected' : '' }}>
                                        {{ __('messages.main_categories') }}
                                    </option>
                                    <option value="sub" {{ request('category_type') === 'sub' ? 'selected' : '' }}>
                                        {{ __('messages.subcategories') }}
                                    </option>
                                </select>
                                <!-- Hidden fields to preserve other filters -->
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="parent_id" value="{{ request('parent_id') }}">
                                <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                            </form>
                        </div>

                        <!-- Parent Category Filter -->
                        <div class="col-md-3">
                            <form method="GET" action="{{ route('home-xrays.index') }}" id="parentForm">
                                <select name="parent_id" class="form-control" onchange="document.getElementById('parentForm').submit();">
                                    <option value="">{{ __('messages.all_parent_categories') }}</option>
                                    @foreach($parentCategories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ request('parent_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <!-- Hidden fields to preserve other filters -->
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="category_type" value="{{ request('category_type') }}">
                                <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                            </form>
                        </div>

                        <!-- Clear Filters -->
                        <div class="col-md-3">
                            <a href="{{ route('home-xrays.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times"></i> {{ __('messages.clear_filters') }}
                            </a>
                        </div>
                    </div>

                    <!-- Price Range Filters -->
                    <div class="row mb-3">
                        <div class="col-md-9">
                            <form method="GET" action="{{ route('home-xrays.index') }}" class="d-flex">
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
                                <!-- Hidden fields to preserve other filters -->
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <input type="hidden" name="category_type" value="{{ request('category_type') }}">
                                <input type="hidden" name="parent_id" value="{{ request('parent_id') }}">
                            </form>
                        </div>
                        <div class="col-md-3">
                            <!-- Statistics -->
                            <div class="alert alert-info mb-0">
                                <strong>{{ __('messages.total_items') }}:</strong> {{ $homeXrays->total() }}
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.id') }}</th>
                                    <th>{{ __('messages.category_name') }}</th>
                                    <th>{{ __('messages.type') }}</th>
                                    <th>{{ __('messages.parent_category') }}</th>
                                    <th>{{ __('messages.price') }}</th>
                                    <th>{{ __('messages.subcategories_count') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($homeXrays as $xray)
                                    <tr class="{{ $xray->isSubcategory() ? 'table-light' : '' }}">
                                        <td>{{ $xray->id }}</td>
                                        <td>
                                            @if($xray->isSubcategory())
                                                <span class="ms-3">
                                                    <i class="fas fa-arrow-right text-muted"></i>
                                                    {{ $xray->name }}
                                                </span>
                                            @else
                                                <strong>{{ $xray->name }}</strong>
                                            @endif
                                        </td>
                                        <td>
                                            @if($xray->isMainCategory())
                                                <span class="badge bg-primary">{{ __('messages.main_category') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('messages.subcategory') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($xray->parent)
                                                <span class="text-muted">{{ $xray->parent->name }}</span>
                                            @else
                                                <span class="text-muted">{{ __('messages.none') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $xray->formatted_price }}</td>
                                        <td>
                                            @if($xray->isMainCategory())
                                                <span class="badge bg-info">{{ $xray->children->count() }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $xray->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                               
                                                <a href="{{ route('home-xrays.edit', $xray) }}" 
                                                   class="btn btn-sm btn-warning" title="{{ __('messages.edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('home-xrays.destroy', $xray) }}" 
                                                      method="POST" 
                                                      style="display: inline;"
                                                      onsubmit="return confirmDelete('{{ $xray->name }}', {{ $xray->children->count() }})">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger"
                                                            title="{{ __('messages.delete') }}"
                                                            {{ $xray->children->count() > 0 ? 'disabled' : '' }}>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            {{ __('messages.no_data_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $homeXrays->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function confirmDelete(categoryName, subcategoriesCount) {
    if (subcategoriesCount > 0) {
        alert('{{ __("messages.cannot_delete_category_with_subcategories") }}');
        return false;
    }
    
    return confirm('{{ __("messages.confirm_delete_category") }}'.replace(':name', categoryName));
}
</script>
@endsection