@extends('layouts.admin')

@section('title', __('messages.edit_elderly_care_type'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.edit_elderly_care_type') }}</h3>
                    <a href="{{ route('elderly-cares.index') }}" class="btn btn-secondary">
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

                    <form action="{{ route('elderly-cares.update', $elderlyCare) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type_of_care" class="form-label">
                                        {{ __('messages.care_type') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="type_of_care" 
                                            id="type_of_care" 
                                            class="form-control @error('type_of_care') is-invalid @enderror" 
                                            required>
                                        <option value="">{{ __('messages.select_care_type') }}</option>
                                        @foreach(\App\Models\TypeElderlyCare::getCareTypes() as $key => $value)
                                            <option value="{{ $key }}" 
                                                {{ (old('type_of_care', $elderlyCare->type_of_care) == $key) ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type_of_care')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type_of_service" class="form-label">
                                        {{ __('messages.service_type') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="type_of_service" 
                                            id="type_of_service" 
                                            class="form-control @error('type_of_service') is-invalid @enderror" 
                                            required>
                                        <option value="">{{ __('messages.select_service_type') }}</option>
                                        @foreach(\App\Models\TypeElderlyCare::getServiceTypes() as $key => $value)
                                            <option value="{{ $key }}" 
                                                {{ (old('type_of_service', $elderlyCare->type_of_service) == $key) ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type_of_service')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="price" class="form-label">
                                        {{ __('messages.price') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="price" 
                                               id="price" 
                                               class="form-control @error('price') is-invalid @enderror" 
                                               value="{{ old('price', $elderlyCare->price) }}" 
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
                                    <a href="{{ route('elderly-cares.index') }}" class="btn btn-secondary me-2">
                                        {{ __('messages.cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('messages.update') }}
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