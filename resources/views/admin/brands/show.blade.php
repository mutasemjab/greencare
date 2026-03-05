@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.brand_details') }}</h3>
                        <div>
                            @can('brand-edit')
                                <a href="{{ route('brands.edit', $brand) }}" class="btn btn-warning btn-sm me-2">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('brands.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Brand Info -->
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            @if($brand->photo)
                                <img src="{{ asset('assets/admin/uploads/' . $brand->photo) }}"
                                     alt="{{ $brand->name_en }}"
                                     class="img-thumbnail mb-3"
                                     style="max-width: 200px;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center mb-3"
                                     style="width: 200px; height: 200px; margin: 0 auto;">
                                    <span class="text-muted">{{ __('messages.no_image') }}</span>
                                </div>
                            @endif

                            <h4>{{ $brand->name_en }}</h4>
                            <p class="text-muted">{{ $brand->name_ar }}</p>
                        </div>

                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle"></i> {{ __('messages.brand_information') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>{{ __('messages.name_en') }}:</strong></td>
                                                    <td>{{ $brand->name_en }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('messages.name_ar') }}:</strong></td>
                                                    <td>{{ $brand->name_ar }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('messages.products_count') }}:</strong></td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $brand->products->count() }}</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>{{ __('messages.created_at') }}:</strong></td>
                                                    <td>{{ $brand->created_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>{{ __('messages.updated_at') }}:</strong></td>
                                                    <td>{{ $brand->updated_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products -->
                    @if($brand->products->count() > 0)
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-box"></i> {{ __('messages.products') }}
                                    <span class="badge bg-light text-success ms-2">{{ $brand->products->count() }}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($brand->products as $product)
                                        <div class="col-md-3 mb-3">
                                            <div class="card product-card">
                                                @if($product->images && count($product->images) > 0)
                                                    <img src="{{ asset('assets/admin/uploads/' . $product->images->first()->image) }}"
                                                         alt="{{ $product->name_en }}"
                                                         class="card-img-top"
                                                         style="height: 150px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                                         style="height: 150px;">
                                                        <span class="text-muted">No Image</span>
                                                    </div>
                                                @endif
                                                <div class="card-body">
                                                    <h6 class="card-title">{{ Str::limit($product->name_en, 30) }}</h6>
                                                    <p class="card-text">
                                                        <small class="text-muted">
                                                            @if($product->category)
                                                                {{ $product->category->name }}
                                                            @endif
                                                        </small>
                                                    </p>
                                                    <p class="card-text">
                                                        <strong>{{ $product->price ?? '-' }}</strong>
                                                    </p>
                                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                                        {{ __('messages.view') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> {{ __('messages.no_products_found') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
