@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('messages.create_transfer') }}</h4>
                    <a href="{{ route('transfer-patients.index') }}" class="btn btn-secondary btn-sm">
                        {{ __('messages.back_to_list') }}
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('transfer-patients.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">{{ __('messages.user') }} <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                    <option value="">{{ __('messages.select_user') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
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
                                       value="{{ old('date_of_transfer', date('Y-m-d')) }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('date_of_transfer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="time_of_transfer" class="form-label">{{ __('messages.time_of_transfer') }}</label>
                                <input type="time" name="time_of_transfer" id="time_of_transfer" 
                                       class="form-control @error('time_of_transfer') is-invalid @enderror" 
                                       value="{{ old('time_of_transfer') }}">
                                @error('time_of_transfer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h5>{{ __('messages.from_location') }}</h5>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="from_address" class="form-label">{{ __('messages.from_address') }} (Google Maps Link) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="from_address" id="from_address" 
                                           class="form-control @error('from_address') is-invalid @enderror" 
                                           value="{{ old('from_address') }}" 
                                           placeholder="Paste Google Maps link here" required>
                                    <button type="button" class="btn btn-outline-primary" onclick="window.open('https://www.google.com/maps', '_blank')">
                                        <i class="bi bi-map"></i> Google Maps
                                    </button>
                                    @error('from_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Paste a Google Maps link to automatically extract coordinates</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="from_place" class="form-label">{{ __('messages.from_place') }} <span class="text-danger">*</span></label>
                                <select name="from_place" id="from_place" class="form-control @error('from_place') is-invalid @enderror" required>
                                    <option value="1" {{ old('from_place', 1) == 1 ? 'selected' : '' }}>{{ __('messages.inside_amman') }}</option>
                                    <option value="2" {{ old('from_place') == 2 ? 'selected' : '' }}>{{ __('messages.outside_amman') }}</option>
                                </select>
                                @error('from_place')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="from_lat" class="form-label">{{ __('messages.latitude') }}</label>
                                <input type="number" step="any" name="from_lat" id="from_lat" 
                                       class="form-control @error('from_lat') is-invalid @enderror" 
                                       value="{{ old('from_lat') }}" 
                                       placeholder="31.9454" readonly>
                                @error('from_lat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="from_lng" class="form-label">{{ __('messages.longitude') }}</label>
                                <input type="number" step="any" name="from_lng" id="from_lng" 
                                       class="form-control @error('from_lng') is-invalid @enderror" 
                                       value="{{ old('from_lng') }}" 
                                       placeholder="35.9284" readonly>
                                @error('from_lng')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h5>{{ __('messages.to_location') }}</h5>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="to_address" class="form-label">{{ __('messages.to_address') }} (Google Maps Link) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="to_address" id="to_address" 
                                           class="form-control @error('to_address') is-invalid @enderror" 
                                           value="{{ old('to_address') }}" 
                                           placeholder="Paste Google Maps link here" required>
                                    <button type="button" class="btn btn-outline-primary" onclick="window.open('https://www.google.com/maps', '_blank')">
                                        <i class="bi bi-map"></i> Google Maps
                                    </button>
                                    @error('to_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Paste a Google Maps link to automatically extract coordinates</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="to_place" class="form-label">{{ __('messages.to_place') }} <span class="text-danger">*</span></label>
                                <select name="to_place" id="to_place" class="form-control @error('to_place') is-invalid @enderror" required>
                                    <option value="1" {{ old('to_place', 1) == 1 ? 'selected' : '' }}>{{ __('messages.inside_amman') }}</option>
                                    <option value="2" {{ old('to_place') == 2 ? 'selected' : '' }}>{{ __('messages.outside_amman') }}</option>
                                </select>
                                @error('to_place')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="to_lat" class="form-label">{{ __('messages.latitude') }}</label>
                                <input type="number" step="any" name="to_lat" id="to_lat" 
                                       class="form-control @error('to_lat') is-invalid @enderror" 
                                       value="{{ old('to_lat') }}" 
                                       placeholder="31.9454" readonly>
                                @error('to_lat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="to_lng" class="form-label">{{ __('messages.longitude') }}</label>
                                <input type="number" step="any" name="to_lng" id="to_lng" 
                                       class="form-control @error('to_lng') is-invalid @enderror" 
                                       value="{{ old('to_lng') }}" 
                                       placeholder="35.9284" readonly>
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
                                      placeholder="{{ __('messages.enter_note') }}">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('transfer-patients.index') }}" class="btn btn-secondary me-md-2">
                                {{ __('messages.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ __('messages.create') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to extract coordinates from Google Maps URL
    function extractCoordinates(url) {
        let lat = null;
        let lng = null;

        // Pattern 1: @lat,lng format (most common)
        let match = url.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
        if (match) {
            lat = parseFloat(match[1]);
            lng = parseFloat(match[2]);
        }

        // Pattern 2: q=lat,lng format
        if (!lat && !lng) {
            match = url.match(/q=(-?\d+\.\d+),(-?\d+\.\d+)/);
            if (match) {
                lat = parseFloat(match[1]);
                lng = parseFloat(match[2]);
            }
        }

        // Pattern 3: ll=lat,lng format
        if (!lat && !lng) {
            match = url.match(/ll=(-?\d+\.\d+),(-?\d+\.\d+)/);
            if (match) {
                lat = parseFloat(match[1]);
                lng = parseFloat(match[2]);
            }
        }

        // Pattern 4: !3d and !4d format (sometimes used)
        if (!lat && !lng) {
            let latMatch = url.match(/!3d(-?\d+\.\d+)/);
            let lngMatch = url.match(/!4d(-?\d+\.\d+)/);
            if (latMatch && lngMatch) {
                lat = parseFloat(latMatch[1]);
                lng = parseFloat(lngMatch[1]);
            }
        }

        return { lat, lng };
    }

    // Handle From Address
    document.getElementById('from_address').addEventListener('input', function(e) {
        const url = e.target.value;
        const coords = extractCoordinates(url);
        
        if (coords.lat && coords.lng) {
            document.getElementById('from_lat').value = coords.lat;
            document.getElementById('from_lng').value = coords.lng;
            e.target.classList.remove('is-invalid');
            e.target.classList.add('is-valid');
        } else if (url.includes('google.com/maps') || url.includes('goo.gl/maps')) {
            e.target.classList.remove('is-valid');
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-valid', 'is-invalid');
            document.getElementById('from_lat').value = '';
            document.getElementById('from_lng').value = '';
        }
    });

    // Handle To Address
    document.getElementById('to_address').addEventListener('input', function(e) {
        const url = e.target.value;
        const coords = extractCoordinates(url);
        
        if (coords.lat && coords.lng) {
            document.getElementById('to_lat').value = coords.lat;
            document.getElementById('to_lng').value = coords.lng;
            e.target.classList.remove('is-invalid');
            e.target.classList.add('is-valid');
        } else if (url.includes('google.com/maps') || url.includes('goo.gl/maps')) {
            e.target.classList.remove('is-valid');
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-valid', 'is-invalid');
            document.getElementById('to_lat').value = '';
            document.getElementById('to_lng').value = '';
        }
    });
});
</script>
@endsection