@extends('layouts.admin')

@section('title', __('messages.edit_provider_category'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.edit_provider_category') }}</h1>
        <a href="{{ route('provider-categories.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ __('messages.back') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.edit_provider_category') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('provider-categories.update', $providerCategory->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name_en">{{ __('messages.category_name_en') }} <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name_en') is-invalid @enderror" 
                                   id="name_en" 
                                   name="name_en" 
                                   value="{{ old('name_en', $providerCategory->name_en) }}" 
                                   required>
                            @error('name_en')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name_ar">{{ __('messages.category_name_ar') }} <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name_ar') is-invalid @enderror" 
                                   id="name_ar" 
                                   name="name_ar" 
                                   value="{{ old('name_ar', $providerCategory->name_ar) }}" 
                                   required
                                   dir="rtl">
                            @error('name_ar')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="type_id">{{ __('messages.type') }} <span class="text-danger">*</span></label>
                    <select class="form-control @error('type_id') is-invalid @enderror" 
                            id="type_id" 
                            name="type_id" 
                            required>
                        <option value="">{{ __('messages.select_type') }}</option>
                        @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ (old('type_id', $providerCategory->type_id) == $type->id) ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? $type->name_ar : $type->name_en }}
                        </option>
                        @endforeach
                    </select>
                    @error('type_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="photo">{{ __('messages.category_photo') }}</label>
                    @if($providerCategory->photo)
                    <div class="mb-2">
                        <img src="{{ asset('assets/admin/uploads/' . $providerCategory->photo) }}" alt="{{ $providerCategory->name_en }}" 
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
                    <a href="{{ route('provider-categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection