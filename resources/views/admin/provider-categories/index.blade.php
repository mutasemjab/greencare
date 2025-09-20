@extends('layouts.admin')

@section('title', __('messages.provider_categories'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.provider_categories') }}</h1>
        @can('providerCategory-add')
        <a href="{{ route('provider-categories.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> {{ __('messages.add_provider_category') }}
        </a>
        @endcan
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.provider_categories') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.id') }}</th>
                            <th>{{ __('messages.image') }}</th>
                            <th>{{ __('messages.category_name_en') }}</th>
                            <th>{{ __('messages.category_name_ar') }}</th>
                            <th>{{ __('messages.type') }}</th>
                            <th>{{ __('messages.created_at') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($providerCategories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>
                                @if($category->photo)
                                <img src="{{ asset('assets/admin/uploads/' . $category->photo) }}" alt="{{ $category->name_en }}" 
                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                <span class="text-muted">{{ __('messages.no_image') }}</span>
                                @endif
                            </td>
                            <td>{{ $category->name_en }}</td>
                            <td>{{ $category->name_ar }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ app()->getLocale() === 'ar' ? $category->type->name_ar : $category->type->name_en }}
                                </span>
                            </td>
                            <td>{{ $category->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @can('providerCategory-edit')
                                <a href="{{ route('provider-categories.edit', $category->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                                @endcan
                                @can('providerCategory-delete')
                                <form action="{{ route('provider-categories.destroy', $category->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('{{ __('messages.are_you_sure') }}')">
                                        <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $providerCategories->links() }}
            </div>
        </div>
    </div>
</div>

@endsection