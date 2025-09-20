@extends('layouts.admin')

@section('title', __('messages.edit') . ' ' . __('messages.Settings'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ __('messages.edit') }} {{ __('messages.Settings') }}</h1>
    <a href="{{ route('settings.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('settings.update', $setting->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">{{ __('messages.phone') }}</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone', $setting->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('messages.email') }}</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $setting->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">{{ __('messages.address') }}</label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                       id="address" name="address" value="{{ old('address', $setting->address) }}" required>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
              <div class="mb-3">
                <label for="twitter" class="form-label">{{ __('messages.twitter') }}</label>
                <input type="text" class="form-control @error('twitter') is-invalid @enderror" 
                       id="twitter" name="twitter" value="{{ old('twitter', $setting->twitter) }}" required>
                @error('twitter')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="instagram" class="form-label">{{ __('messages.instagram') }}</label>
                <input type="text" class="form-control @error('instagram') is-invalid @enderror" 
                       id="instagram" name="instagram" value="{{ old('instagram', $setting->instagram) }}" required>
                @error('instagram')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="facebook" class="form-label">{{ __('messages.facebook') }}</label>
                <input type="text" class="form-control @error('facebook') is-invalid @enderror" 
                       id="facebook" name="facebook" value="{{ old('facebook', $setting->facebook) }}" required>
                @error('facebook')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="google_map" class="form-label">{{ __('messages.google_map') }}</label>
                <textarea class="form-control @error('google_map') is-invalid @enderror" 
                          id="google_map" name="google_map" rows="4" required>{{ old('google_map', $setting->google_map) }}</textarea>
                @error('google_map')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Enter Google Maps embed code</small>
            </div>
            
            <div class="mb-3">
                <label for="logo" class="form-label">{{ __('messages.logo') }}</label>
                @if($setting->logo)
                    <div class="mb-2">
                        <img src="{{ asset($setting->logo) }}" alt="Current Logo" class="img-thumbnail" style="max-height: 200px;">
                    </div>
                @endif
                <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                       id="logo" name="logo" accept="image/*">
                @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-end">
                <a href="{{ route('settings.index') }}" class="btn btn-secondary me-2">
                    {{ __('messages.cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection