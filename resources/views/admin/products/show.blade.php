@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.Product_Details') }}</h3>
                    <div>
                        @can('product-edit')
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> {{ __('messages.Edit') }}
                            </a>
                        @endcan
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.Back') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Product Images -->
                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('messages.Product_Images') }}</h5>
                                </div>
                                <div class="card-body">
                                    @if($product->images->count() > 0)
                                        
                                        
                                        @if($product->images->count() > 1)
                                            <div class="row mt-3">
                                                @foreach($product->images as $index => $image)
                                                    <div class="col-3">
                                                        <img src="{{ asset('assets/admin/uploads/'.$image->photo) }}" 
                                                             class="img-fluid rounded cursor-pointer thumbnail-image" 
                                                             style="height: 80px; object-fit: cover;"
                                                             data-bs-target="#productCarousel" 
                                                             data-bs-slide-to="{{ $index }}"
                                                             alt="Thumbnail {{ $index + 1 }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <div class="text-center py-5">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                            <p class="mt-2 text-muted">{{ __('messages.No_Images_Available') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Product Information -->
                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('messages.Product_Information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Name_English') }}:</label>
                                            <p>{{ $product->name_en }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Name_Arabic') }}:</label>
                                            <p>{{ $product->name_ar }}</p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Description_English') }}:</label>
                                            <p>{{ $product->description_en }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Description_Arabic') }}:</label>
                                            <p>{{ $product->description_ar }}</p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('messages.Price') }}:</label>
                                            <p class="fs-4 text-primary fw-bold">JD {{ number_format($product->price, 2) }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('messages.Tax_Percentage') }}:</label>
                                            <p>{{ $product->tax }}%</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ __('messages.Discount') }}:</label>
                                            @if($product->discount_percentage)
                                                <p class="text-success">
                                                    <span class="badge bg-success">{{ $product->discount_percentage }}%</span>
                                                    <br>
                                                    <small>{{ __('messages.Price_After_Discount') }}: JD {{ number_format($product->price_after_discount, 2) }}</small>
                                                </p>
                                            @else
                                                <p class="text-muted">{{ __('messages.No_Discount') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Category') }}:</label>
                                            <p>
                                                @if($product->category)
                                                    <span class="badge bg-primary">
                                                        {{ app()->getLocale() == 'ar' ? $product->category->name_ar : $product->category->name_en }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">{{ __('messages.No_Category') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                       
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Brand') }}:</label>
                                            <p>
                                                @if($product->brand)
                                                    <span class="badge bg-info">
                                                        {{ app()->getLocale() == 'ar' ? $product->brand->name_ar : $product->brand->name_en }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">{{ __('messages.No_Brand') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                        
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Created_At') }}:</label>
                                            <p>{{ $product->created_at->format('Y-m-d H:i:s') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">{{ __('messages.Updated_At') }}:</label>
                                            <p>{{ $product->updated_at->format('Y-m-d H:i:s') }}</p>
                                        </div>
                                    </div>
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

