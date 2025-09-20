@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('messages.Edit_Category') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name_en" class="form-label">
                                {{ __('messages.Name_English') }}
                            </label>
                            <input type="text" 
                                   class="form-control @error('name_en') is-invalid @enderror" 
                                   id="name_en" 
                                   name="name_en" 
                                   value="{{ old('name_en', $category->name_en) }}" 
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
                                   value="{{ old('name_ar', $category->name_ar) }}" 
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
                                            {{ old('category_id', $category->category_id) == $parentCategory->id ? 'selected' : '' }}>
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

                           <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ __('messages.category_Photo') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Current Photo -->
                                        @if($category->photo)
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('messages.Current_Photo') }}:</label>
                                                <div class="text-center">
                                                    <img src="{{ asset('assets/admin/uploads/'.$category->photo) }}" 
                                                         alt="{{ $category->name_en }}" 
                                                         class="img-fluid rounded border" 
                                                         style="width: 100%; height: 200px; object-fit: cover;">
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <label class="form-label">
                                                {{ $category->photo ? __('messages.Change_Photo') : __('messages.Photo') }}
                                                @if(!$category->photo)<span class="text-danger">*</span>@endif
                                            </label>
                                            <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                                   name="photo" accept="image/*" {{ !$category->photo ? 'required' : '' }}>
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
                                {{ __('messages.Update') }}
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