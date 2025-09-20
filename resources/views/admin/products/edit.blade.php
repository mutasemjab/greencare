@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.Edit_Product') }}</h3>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.Back') }}
                        </a>
                    </div>

                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
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

                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('messages.Basic_Information') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('messages.Name_English') }} <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control @error('name_en') is-invalid @enderror"
                                                            name="name_en" value="{{ old('name_en', $product->name_en) }}"
                                                            required>
                                                        @error('name_en')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('messages.Name_Arabic') }} <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control @error('name_ar') is-invalid @enderror"
                                                            name="name_ar" value="{{ old('name_ar', $product->name_ar) }}"
                                                            required>
                                                        @error('name_ar')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                               
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('messages.Description_English') }}
                                                            <span class="text-danger">*</span></label>
                                                        <textarea class="form-control @error('description_en') is-invalid @enderror" name="description_en" rows="4"
                                                            required>{{ old('description_en', $product->description_en) }}</textarea>
                                                        @error('description_en')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('messages.Description_Arabic') }}
                                                            <span class="text-danger">*</span></label>
                                                        <textarea class="form-control @error('description_ar') is-invalid @enderror" name="description_ar" rows="4"
                                                            required>{{ old('description_ar', $product->description_ar) }}</textarea>
                                                        @error('description_ar')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('messages.Price') }} <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number" step="0.01"
                                                            class="form-control @error('price') is-invalid @enderror"
                                                            name="price" value="{{ old('price', $product->price) }}"
                                                            required>
                                                        @error('price')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label">{{ __('messages.Tax_Percentage') }}</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control @error('tax') is-invalid @enderror"
                                                            name="tax" value="{{ old('tax', $product->tax) }}">
                                                        @error('tax')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label
                                                            class="form-label">{{ __('messages.Discount_Percentage') }}</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control @error('discount_percentage') is-invalid @enderror"
                                                            name="discount_percentage"
                                                            value="{{ old('discount_percentage', $product->discount_percentage) }}">
                                                        @error('discount_percentage')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Current Images -->
                                    @if ($product->images->count() > 0)
                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h5>{{ __('messages.Current_Images') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach ($product->images as $image)
                                                        <div class="col-md-3 mb-3">
                                                            <div class="position-relative">
                                                                <img src="{{ asset('assets/admin/uploads/' . $image->photo) }}"
                                                                    class="img-fluid rounded"
                                                                    style="width: 100%; height: 150px; object-fit: cover;">
                                                                <div class="position-absolute top-0 end-0 p-1">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="delete_images[]"
                                                                        value="{{ $image->id }}"
                                                                        id="delete_{{ $image->id }}">
                                                                    <label
                                                                        class="form-check-label bg-danger text-white px-1 rounded"
                                                                        for="delete_{{ $image->id }}">
                                                                        {{ __('messages.Delete') }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Add New Images -->
                                    <div class="card mt-3">
                                        <div class="card-header">
                                            <h5>{{ __('messages.Add_New_Images') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('messages.Images') }}</label>
                                                <input type="file"
                                                    class="form-control @error('images.*') is-invalid @enderror"
                                                    name="images[]" multiple accept="image/*">
                                                <small class="form-text text-muted">
                                                    {{ __('messages.You_Can_Select_Multiple_Images') }}
                                                </small>
                                                @error('images.*')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                   
                                </div>

                                <!-- Relations -->
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>{{ __('messages.Relations') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('messages.Category') }}</label>
                                                <select class="form-control @error('category_id') is-invalid @enderror"
                                                    name="category_id">
                                                    <option value="">{{ __('messages.Select_Category') }}</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                            {{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>


                                            <div class="mb-3">
                                                <label class="form-label">{{ __('messages.Brand') }}</label>
                                                <select class="form-control @error('brand_id') is-invalid @enderror"
                                                    name="brand_id">
                                                    <option value="">{{ __('messages.Select_Brand') }}</option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand->id }}"
                                                            {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                                            {{ app()->getLocale() == 'ar' ? $brand->name_ar : $brand->name_en }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('brand_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('messages.Update_Product') }}
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                {{ __('messages.Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection


