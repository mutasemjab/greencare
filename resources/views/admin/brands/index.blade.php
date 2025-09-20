@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.Brands') }}</h3>
                    @can('brand-add')
                        <a href="{{ route('brands.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.Add_Brand') }}
                        </a>
                    @endcan
                </div>

                <div class="card-body">
                

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.Photo') }}</th>
                                    <th>{{ __('messages.Name_English') }}</th>
                                    <th>{{ __('messages.Name_Arabic') }}</th>
                                    <th>{{ __('messages.Products_Count') }}</th>
                                    <th>{{ __('messages.Created_At') }}</th>
                                    <th>{{ __('messages.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $brand)
                                    <tr>
                                        <td>{{ $loop->iteration + ($brands->currentPage() - 1) * $brands->perPage() }}</td>
                                        <td>
                                            @if($brand->photo)
                                                <img src="{{ asset('assets/admin/uploads/'.$brand->photo) }}" 
                                                     alt="{{ $brand->name_en }}" 
                                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-tags text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $brand->name_en }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ $brand->name_ar }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $brand->products_count ?? 0 }} {{ __('messages.Products') }}
                                            </span>
                                        </td>
                                        <td>{{ $brand->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                
                                                @can('brand-edit')
                                                    <a href="{{ route('brands.edit', $brand) }}" 
                                                       class="btn btn-sm btn-warning" title="{{ __('messages.Edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                             
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('messages.No_Brands_Found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $brands->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

