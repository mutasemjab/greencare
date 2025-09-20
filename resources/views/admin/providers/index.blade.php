@extends('layouts.admin')

@section('title', __('messages.providers'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.providers') }}</h1>
        @can('provider-add')
        <a href="{{ route('providers.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> {{ __('messages.add_provider') }}
        </a>
        @endcan
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.providers') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.id') }}</th>
                            <th>{{ __('messages.image') }}</th>
                            <th>{{ __('messages.provider_name') }}</th>
                            <th>{{ __('messages.years_experience') }}</th>
                            <th>{{ __('messages.price') }}</th>
                            <th>{{ __('messages.provider_category') }}</th>
                            <th>{{ __('messages.created_at') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($providers as $provider)
                        <tr>
                            <td>{{ $provider->id }}</td>
                            <td>
                                @if($provider->photo)
                                <img src="{{ asset('assets/admin/uploads/' . $provider->photo) }}" alt="{{ $provider->name }}" 
                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                <span class="text-muted">{{ __('messages.no_image') }}</span>
                                @endif
                            </td>
                            <td>{{ $provider->name }}</td>
                            <td>{{ $provider->number_years_experience }}</td>
                            <td>
                                <span class="badge badge-success">JD {{ number_format($provider->price, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ app()->getLocale() === 'ar' ? $provider->providerCategory->name_ar : $provider->providerCategory->name_en }}
                                </span>
                            </td>
                            <td>{{ $provider->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @can('provider-edit')
                                <a href="{{ route('providers.edit', $provider->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                                @endcan
                                @can('provider-delete')
                                <form action="{{ route('providers.destroy', $provider->id) }}" method="POST" style="display: inline-block;">
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
                {{ $providers->links() }}
            </div>
        </div>
    </div>
</div>


@endsection