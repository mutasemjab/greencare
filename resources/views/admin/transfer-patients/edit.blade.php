@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('messages.edit_transfer') }}</h4>
                    <a href="{{ route('transfer-patients.index') }}" class="btn btn-secondary btn-sm">
                        {{ __('messages.back_to_list') }}
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('transfer-patients.update', $transferPatient) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">{{ __('messages.user') }} <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">{{ __('messages.select_user') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $transferPatient->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="date_of_transfer" class="form-label">{{ __('messages.date_of_transfer') }} <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_transfer" id="date_of_transfer" 
                                       class="form-control @error('date_of_transfer') is-invalid @enderror" 
                                       value="{{ old('date_of_transfer', $transferPatient->date_of_transfer->format('Y-m-d')) }}" 
                                       required>
                                @error('date_of_transfer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="time_of_transfer" class="form-label">{{ __('messages.time_of_transfer') }}</label>
                                <input type="time" name="time_of_transfer" id="time_of_transfer" 
                                       class="form-control @error('time_of_transfer') is-invalid @enderror" 
                                       value="{{ old('time_of_transfer', $transferPatient->time_of_transfer ? $transferPatient->time_of_transfer->format('H:i') : '') }}">
                                @error('time_of_transfer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h5>{{ __('messages.from_location') }}</h5>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="from_address" class="form-label">{{ __('messages.from_address') }} <span class="text-danger">*</span></label>
                                <input type="text" name="from_address" id="from_address" 
                                       class="form-control @error('from_address') is-invalid @enderror" 
                                       value="{{ old('from_address', $transferPatient->from_address) }}" 
                                       placeholder="{{ __('messages.enter_from_address') }}" required>
                                @error('from_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="from_place" class="form-label">{{ __('messages.from_place') }} <span class="text-danger">*</span></label>
                                <select name="from_place" id="from_place" class="form-select @error('from_place') is-invalid @enderror" required>
                                    <option value="1" {{ old('from_place', $transferPatient->from_place) == 1 ? 'selected' : '' }}>{{ __('messages.inside_amman') }}</option>
                                    <option value="2" {{ old('from_place', $transferPatient->from_place) == 2 ? 'selected' : '' }}>{{ __('messages.outside_amman') }}</option>
                                </select>
                                @error('from_place')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="from_lat" class="form-label">{{ __('messages.latitude') }}</label>
                                <input type="number" step="any" name="from_lat" id="from_lat" 
                                       class="form-control @error('from_lat') is-invalid @enderror" 
                                       value="{{ old('from_lat', $transferPatient->from_lat) }}" 
                                       placeholder="31.9454">
                                @error('from_lat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="from_lng" class="form-label">{{ __('messages.longitude') }}</label>
                                <input type="number" step="any" name="from_lng" id="from_lng" 
                                       class="form-control @error('from_lng') is-invalid @enderror" 
                                       value="{{ old('from_lng', $transferPatient->from_lng) }}" 
                                       placeholder="35.9284">
                                @error('from_lng')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h5>{{ __('messages.to_location') }}</h5>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="to_address" class="form-label">{{ __('messages.to_address') }} <span class="text-danger">*</span></label>
                                <input type="text" name="to_address" id="to_address" 
                                       class="form-control @error('to_address') is-invalid @enderror" 
                                       value="{{ old('to_address', $transferPatient->to_address) }}" 
                                       placeholder="{{ __('messages.enter_to_address') }}" required>
                                @error('to_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="to_place" class="form-label">{{ __('messages.to_place') }} <span class="text-danger">*</span></label>
                                <select name="to_place" id="to_place" class="form-select @error('to_place') is-invalid @enderror" required>
                                    <option value="1" {{ old('to_place', $transferPatient->to_place) == 1 ? 'selected' : '' }}>{{ __('messages.inside_amman') }}</option>
                                    <option value="2" {{ old('to_place', $transferPatient->to_place) == 2 ? 'selected' : '' }}>{{ __('messages.outside_amman') }}</option>
                                </select>
                                @error('to_place')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="to_lat" class="form-label">{{ __('messages.latitude') }}</label>
                                <input type="number" step="any" name="to_lat" id="to_lat" 
                                       class="form-control @error('to_lat') is-invalid @enderror" 
                                       value="{{ old('to_lat', $transferPatient->to_lat) }}" 
                                       placeholder="31.9454">
                                @error('to_lat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="to_lng" class="form-label">{{ __('messages.longitude') }}</label>
                                <input type="number" step="any" name="to_lng" id="to_lng" 
                                       class="form-control @error('to_lng') is-invalid @enderror" 
                                       value="{{ old('to_lng', $transferPatient->to_lng) }}" 
                                       placeholder="35.9284">
                                @error('to_lng')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <div class="mb-3">
                            <label for="note" class="form-label">{{ __('messages.note') }}</label>
                            <textarea name="note" id="note" rows="3" 
                                      class="form-control @error('note') is-invalid @enderror" 
                                      placeholder="{{ __('messages.enter_note') }}">{{ old('note', $transferPatient->note) }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('transfer-patients.index') }}" class="btn btn-secondary me-md-2">
                                {{ __('messages.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ __('messages.update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection