@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('messages.Add_Category') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name_en" class="form-label">
                                {{ __('messages.Name_English') }}
                            </label>
                            <input type="text" 
                                   class="form-control @error('name_en') is-invalid @enderror" 
                                   id="name_en" 
                                   name="name_en" 
                                   value="{{ old('name_en') }}" 
                                   placeholder="{{ __('messages.Enter_English_Name') }}"
                                   required>
                            @error('name_en')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name_ar" class="form-label">
                                {{ __('messages.Name_Arabic') }}
                            </label>
                            <input type="text" 
                                   class="form-control @error('name_ar') is-invalid @enderror" 
                                   id="name_ar" 
                                   name="name_ar" 
                                   value="{{ old('name_ar') }}" 
                                   placeholder="{{ __('messages.Enter_Arabic_Name') }}"
                                   required>
                            @error('name_ar')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                      

                        <div class="mb-3">
                            <label for="category_id" class="form-label">
                                {{ __('messages.Parent_Category') }}
                            </label>
                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                    id="category_id" 
                                    name="category_id">
                                <option value="">{{ __('messages.Select_Parent_Category') }}</option>
                                @foreach($parentCategories as $parentCategory)
                                    <option value="{{ $parentCategory->id }}" 
                                            {{ old('category_id') == $parentCategory->id ? 'selected' : '' }}>
                                        {{ app()->getLocale() == 'ar' ? $parentCategory->name_ar : $parentCategory->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ __('messages.Leave_Empty_Main_Category') }}</div>
                            @error('category_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                          <div class="col-md-12">
                                <div class="card">
                                   
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                {{  __('messages.Photo') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                                   name="photo" accept="image/*" {{'required' }}>
                                            <small class="form-text text-muted">
                                                {{ __('messages.Allowed_Formats') }}: JPG, JPEG, PNG, GIF
                                            </small>
                                            @error('photo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- New Image Preview -->
                                        <div id="imagePreview" class="mt-3" style="display: none;">
                                            <label class="form-label">{{ __('messages.New_Preview') }}:</label>
                                            <img id="previewImg" src="" alt="Preview" 
                                                 class="img-fluid rounded border" 
                                                 style="width: 100%; height: 200px; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ __('messages.Save') }}
                            </button>
                            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                                {{ __('messages.Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection