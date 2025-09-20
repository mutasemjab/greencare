@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.edit_doctor') }}: {{ $doctor->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('doctors.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('doctors.update', $doctor) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                  

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3">{{ __('messages.basic_information') }}</h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $doctor->name) }}" 
                                           placeholder="{{ __('messages.enter_doctor_name') }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">{{ __('messages.phone') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           name="phone" 
                                           id="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $doctor->phone) }}" 
                                           placeholder="{{ __('messages.enter_phone') }}" 
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('messages.email') }}</label>
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $doctor->email) }}" 
                                           placeholder="{{ __('messages.enter_email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">{{ __('messages.date_of_birth') }} <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           name="date_of_birth" 
                                           id="date_of_birth" 
                                           class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           value="{{ old('date_of_birth', $doctor->date_of_birth->format('Y-m-d')) }}" 
                                           required>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="gender" class="form-label">{{ __('messages.gender') }} <span class="text-danger">*</span></label>
                                    <select name="gender" 
                                            id="gender" 
                                            class="form-control @error('gender') is-invalid @enderror" 
                                            required>
                                        <option value="">{{ __('messages.select_gender') }}</option>
                                        <option value="1" {{ old('gender', $doctor->gender) == '1' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                                        <option value="2" {{ old('gender', $doctor->gender) == '2' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3">{{ __('messages.additional_information') }}</h5>

                                <div class="mb-3">
                                    <label for="photo" class="form-label">{{ __('messages.photo') }}</label>
                                    
                                    <!-- Current Photo Display -->
                                    @if($doctor->photo)
                                        <div class="mb-2">
                                            <img src="{{ asset('assets/admin/uploads/' . $doctor->photo) }}" 
                                                 alt="{{ $doctor->name }}" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 150px;">
                                            <div class="form-text">{{ __('messages.current_photo') }}</div>
                                        </div>
                                    @endif
                                    
                                    <input type="file" 
                                           name="photo" 
                                           id="photo" 
                                           class="form-control @error('photo') is-invalid @enderror" 
                                           accept="image/*"
                                           onchange="previewImage(this)">
                                    <div class="form-text">{{ __('messages.photo_help') }}</div>
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- New Image Preview -->
                                    <div id="imagePreview" class="mt-2" style="display: none;">
                                        <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 150px;">
                                        <div class="form-text">{{ __('messages.new_photo_preview') }}</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="activate" class="form-label">{{ __('messages.status') }} <span class="text-danger">*</span></label>
                                    <select name="activate" 
                                            id="activate" 
                                            class="form-control @error('activate') is-invalid @enderror" 
                                            required>
                                        <option value="">{{ __('messages.select_status') }}</option>
                                        <option value="1" {{ old('activate', $doctor->activate) == '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                        <option value="2" {{ old('activate', $doctor->activate) == '2' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                                    </select>
                                    @error('activate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                               

                                <!-- doctor Information Summary -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ __('messages.current_information') }}</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td><strong>{{ __('messages.doctor_type') }}:</strong></td>
                                                <td><span class="badge bg-info">{{ $doctor->doctor_type_text }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('messages.created_at') }}:</strong></td>
                                                <td>{{ $doctor->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{ __('messages.updated_at') }}:</strong></td>
                                                <td>{{ $doctor->updated_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                            @if($doctor->families->count() > 0)
                                                <tr>
                                                    <td><strong>{{ __('messages.families') }}:</strong></td>
                                                    <td>
                                                        @foreach($doctor->families as $family)
                                                            <span class="badge bg-secondary me-1">{{ $family->name }}</span>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('doctors.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                            </a>
                            <div>
                                @can('doctor-table')
                                    <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-info me-2">
                                        <i class="fas fa-eye"></i> {{ __('messages.view_details') }}
                                    </a>
                                @endcan
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ __('messages.update_doctor') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    const previewDiv = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewDiv.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewDiv.style.display = 'none';
    }
}

// Auto-hide success/error messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.classList.contains('alert-dismissible')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        });
    }, 5000);
});

// Form validation feedback
document.getElementById('name').addEventListener('input', function() {
    if (this.value.length < 2) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    }
});

document.getElementById('phone').addEventListener('input', function() {
    const phoneRegex = /^[\+]?[\d\s\-\(\)]+$/;
    if (!phoneRegex.test(this.value) || this.value.length < 8) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    }
});

document.getElementById('email').addEventListener('input', function() {
    if (this.value === '') {
        this.classList.remove('is-invalid', 'is-valid');
        return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(this.value)) {
        this.classList.add('is-invalid');
    } else {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
    }
});
</script>
@endpush

