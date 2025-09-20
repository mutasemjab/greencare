@extends('layouts.admin')

@section('title', __('messages.Settings'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ __('messages.Settings') }}</h1>
    <a href="{{ route('settings.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> {{ __('messages.create') }}
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($settings->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ __('messages.logo') }}</th>
                            <th>{{ __('messages.phone') }}</th>
                            <th>{{ __('messages.email') }}</th>
                            <th>{{ __('messages.address') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($settings as $setting)
                        <tr>
                            <td>{{ $setting->id }}</td>
                            <td>
                                @if($setting->logo)
                                    <img src="{{ asset($setting->logo) }}" alt="Logo" style="width: 50px; height: 50px; object-fit: cover;">
                                @endif
                            </td>
                            <td>{{ $setting->phone }}</td>
                            <td>{{ $setting->email }}</td>
                            <td>{{ Str::limit($setting->address, 30) }}</td>
                            <td>
                                <a href="{{ route('settings.edit', $setting->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                                <form action="{{ route('settings.destroy', $setting->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-muted">No settings found.</p>
                <a href="{{ route('settings.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('messages.create') }}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection