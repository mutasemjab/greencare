@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.Products') }}</h3>
                    @can('product-add')
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.Add_Product') }}
                        </a>
                    @endcan
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.Image') }}</th>
                                    <th>{{ __('messages.Name') }}</th>
                                    <th>{{ __('messages.Price') }}</th>
                                    <th>{{ __('messages.Discount') }}</th>
                                    <th>{{ __('messages.Category') }}</th>
                                    <th>{{ __('messages.Brand') }}</th>
                                    <th>{{ __('messages.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</td>
                                        <td>
                                            @if($product->images->first())
                                                <img src="{{ asset('assets/admin/uploads/'. $product->images->first()->photo) }}" 
                                                     alt="{{ app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en }}" 
                                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ app()->getLocale() == 'ar' ? $product->name_ar : $product->name_en }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ Str::limit(app()->getLocale() == 'ar' ? $product->description_ar : $product->description_en, 50) }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                                            @if($product->price_after_discount)
                                                <br>
                                                <small class="text-success">
                                                    {{ __('messages.After_Discount') }}: ${{ number_format($product->price_after_discount, 2) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->discount_percentage)
                                                <span class="badge bg-success">{{ $product->discount_percentage }}%</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('messages.No_Discount') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->category)
                                                {{ app()->getLocale() == 'ar' ? $product->category->name_ar : $product->category->name_en }}
                                            @else
                                                <span class="text-muted">{{ __('messages.No_Category') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->brand)
                                                {{ app()->getLocale() == 'ar' ? $product->brand->name_ar : $product->brand->name_en }}
                                            @else
                                                <span class="text-muted">{{ __('messages.No_Brand') }}</span>
                                            @endif
                                        </td>
                                     
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('products.show', $product) }}" 
                                                   class="btn btn-sm btn-info" title="{{ __('messages.View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('product-edit')
                                                    <a href="{{ route('products.edit', $product) }}" 
                                                       class="btn btn-sm btn-warning" title="{{ __('messages.Edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                              
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">{{ __('messages.No_Products_Found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

