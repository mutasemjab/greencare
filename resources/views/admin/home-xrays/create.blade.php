@extends('layouts.admin')

@section('title', __('messages.add_home_xray_type'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.add_home_xray_type') }}</h3>
                    <a href="{{ route('home-xrays.index') }}" class="btn btn-secondary">
                        {{ __('messages.back_to_list') }}
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('home-xrays.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Category Type Selection -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('messages.category_type') }}</label>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="category_type" 
                                               id="main_category" 
                                               value="main" 
                                               {{ old('parent_id') ? '' : 'checked' }}
                                               onchange="toggleParentSelection()">
                                        <label class="form-check-label" for="main_category">
                                            {{ __('messages.main_category') }}
                                            <small class="text-muted">({{ __('messages.main_category_description') }})</small>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="category_type" 
                                               id="sub_category" 
                                               value="sub"
                                               {{ old('parent_id') ? 'checked' : '' }}
                                               onchange="toggleParentSelection()">
                                        <label class="form-check-label" for="sub_category">
                                            {{ __('messages.subcategory') }}
                                            <small class="text-muted">({{ __('messages.subcategory_description') }})</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Parent Category Selection -->
                            <div class="col-md-12" id="parent_selection" style="{{ old('parent_id') ? '' : 'display: none;' }}">
                                <div class="mb-3">
                                    <label for="parent_id" class="form-label">
                                        {{ __('messages.parent_category') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="parent_id" 
                                            id="parent_id" 
                                            class="form-control @error('parent_id') is-invalid @enderror">
                                        <option value="">{{ __('messages.select_parent_category') }}</option>
                                        @foreach($parentCategories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Category Name -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        {{ __('messages.category_name') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" 
                                           placeholder="{{ __('messages.enter_category_name') }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">
                                        {{ __('messages.price') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="price" 
                                               id="price" 
                                               class="form-control @error('price') is-invalid @enderror" 
                                               value="{{ old('price') }}" 
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00"
                                               required>
                                        <span class="input-group-text">{{ __('messages.currency') }}</span>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Information Box -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> {{ __('messages.category_information') }}</h6>
                                    <ul class="mb-0">
                                        <li>{{ __('messages.main_category_info') }}</li>
                                        <li>{{ __('messages.subcategory_info') }}</li>
                                        <li>{{ __('messages.price_info') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('home-xrays.index') }}" class="btn btn-secondary me-2">
                                        {{ __('messages.cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('messages.save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function toggleParentSelection() {
    const mainCategoryRadio = document.getElementById('main_category');
    const parentSelection = document.getElementById('parent_selection');
    const parentSelect = document.getElementById('parent_id');
    
    if (mainCategoryRadio.checked) {
        parentSelection.style.display = 'none';
        parentSelect.value = '';
        parentSelect.removeAttribute('required');
    } else {
        parentSelection.style.display = 'block';
        parentSelect.setAttribute('required', 'required');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleParentSelection();
});
</script>
@endsection