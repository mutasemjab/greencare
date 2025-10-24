@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>{{ __('messages.edit_setting') }}</h2>

    <form action="{{ route('settings.update', $setting->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>{{ __('messages.key') }}</label>
            <input type="text" class="form-control" value="{{ __('messages.' . $setting->key) }}" readonly>
        </div>

        <div class="form-group mt-3">
            <label>{{ __('messages.value') }}</label>
            <input type="number" name="value" class="form-control" value="{{ $setting->value }}" required>
        </div>

        <button type="submit" class="btn btn-success mt-3">{{ __('messages.update') }}</button>
        <a href="{{ route('settings.index') }}" class="btn btn-secondary mt-3">{{ __('messages.cancel') }}</a>
    </form>
</div>
@endsection
