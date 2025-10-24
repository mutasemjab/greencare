@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>{{ __('messages.settings') }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{ __('messages.key') }}</th>
                <th>{{ __('messages.value') }}</th>
                <th>{{ __('messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($settings as $setting)
                <tr>
                    <td>{{ __('messages.' . $setting->key) }}</td>
                    <td>{{ $setting->value }}</td>
                    <td>
                        <a href="{{ route('settings.edit', $setting->id) }}" class="btn btn-primary">
                            {{ __('messages.edit') }}
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
