@extends('layouts.admin')

@section('title', __('messages.edit_provider'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.edit_provider') }}</h1>
        <a href="{{ route('providers.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ __('messages.back') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.edit_provider') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('providers.update', $provider->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{ __('messages.provider_name') }} <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $provider->name) }}" 
                                   required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="number_years_experience">{{ __('messages.years_experience') }} <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('number_years_experience') is-invalid @enderror" 
                                   id="number_years_experience" 
                                   name="number_years_experience" 
                                   value="{{ old('number_years_experience', $provider->number_years_experience) }}" 
                                   required>
                            @error('number_years_experience')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="price">{{ __('messages.price') }} <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('price') is-invalid @enderror" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price', $provider->price) }}" 
                                   step="any"
                                   min="0"
                                   required>
                            @error('price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="price">{{ __('messages.rating') }} <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('rating') is-invalid @enderror" 
                                   id="rating" 
                                   name="rating" 
                                   value="{{ old('rating', $provider->rating) }}" 
                                   step="any"
                                   min="0"
                                   required>
                            @error('rating')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="provider_category_id">{{ __('messages.provider_category') }} <span class="text-danger">*</span></label>
                            <select class="form-control @error('provider_category_id') is-invalid @enderror" 
                                    id="provider_category_id" 
                                    name="provider_category_id" 
                                    required>
                                <option value="">{{ __('messages.select_category') }}</option>
                                @foreach($providerCategories as $category)
                                <option value="{{ $category->id }}" {{ (old('provider_category_id', $provider->provider_category_id) == $category->id) ? 'selected' : '' }}>
                                    {{ app()->getLocale() === 'ar' ? $category->name_ar : $category->name_en }}
                                </option>
                                @endforeach
                            </select>
                            @error('provider_category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">{{ __('messages.description') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="4" 
                              required>{{ old('description', $provider->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="photo">{{ __('messages.provider_photo') }}</label>
                    @if($provider->photo)
                    <div class="mb-2">
                        <img src="{{ asset('assets/admin/uploads/' . $provider->photo) }}" alt="{{ $provider->name }}" 
                             class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    @endif
                    <input type="file" 
                           class="form-control-file @error('photo') is-invalid @enderror" 
                           id="photo" 
                           name="photo" 
                           accept="image/*">
                    @error('photo')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('messages.update') }}
                    </button>
                    <a href="{{ route('providers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection