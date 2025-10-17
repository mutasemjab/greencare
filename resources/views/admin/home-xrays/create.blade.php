@extends('layouts.admin')

@section('title', __('messages.add_home_xray_type'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.add_home_xray_type') }}</h3>
                    <a href="{{ route('home-xrays.index') }}" class="btn btn-secondary">
                        {{ __('messages.back_to_list') }}
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('home-xrays.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        {{ __('messages.xray_name') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" 
                                           placeholder="{{ __('messages.enter_xray_name') }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">
                                        {{ __('messages.price') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="price" 
                                               id="price" 
                                               class="form-control @error('price') is-invalid @enderror" 
                                               value="{{ old('price') }}" 
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00"
                                               required>
                                        <span class="input-group-text">{{ __('messages.currency') }}</span>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('home-xrays.index') }}" class="btn btn-secondary me-2">
                                        {{ __('messages.cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('messages.save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection