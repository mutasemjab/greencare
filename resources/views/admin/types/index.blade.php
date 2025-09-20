@extends('layouts.admin')

@section('title', __('messages.types'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('messages.types') }}</h1>
        @can('type-add')
        <a href="{{ route('types.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> {{ __('messages.add_type') }}
        </a>
        @endcan
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.types') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.id') }}</th>
                            <th>{{ __('messages.image') }}</th>
                            <th>{{ __('messages.type_name_en') }}</th>
                            <th>{{ __('messages.type_name_ar') }}</th>
                            <th>{{ __('messages.created_at') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($types as $type)
                        <tr>
                            <td>{{ $type->id }}</td>
                            <td>
                                @if($type->photo)
                                <img src="{{ asset('assets/admin/uploads/' . $type->photo) }}" alt="{{ $type->name_en }}" 
                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                <span class="text-muted">{{ __('messages.no_image') }}</span>
                                @endif
                            </td>
                            <td>{{ $type->name_en }}</td>
                            <td>{{ $type->name_ar }}</td>
                            <td>{{ $type->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @can('type-edit')
                                <a href="{{ route('types.edit', $type->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                                @endcan
                                @can('type-delete')
                                <form action="{{ route('types.destroy', $type->id) }}" method="POST" style="display: inline-block;">
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
                {{ $types->links() }}
            </div>
        </div>
    </div>
</div>


@endsection