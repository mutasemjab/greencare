@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.add_patient') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>

                <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
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
                                           value="{{ old('name') }}" 
                                           placeholder="{{ __('messages.enter_patient_name') }}" 
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
                                           value="{{ old('phone') }}" 
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
                                           value="{{ old('email') }}" 
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
                                           value="{{ old('date_of_birth') }}" 
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
                                        <option value="1" {{ old('gender') == '1' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                                        <option value="2" {{ old('gender') == '2' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
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
                                    
                                    <!-- Image Preview -->
                                    <div id="imagePreview" class="mt-2" style="display: none;">
                                        <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="activate" class="form-label">{{ __('messages.status') }} <span class="text-danger">*</span></label>
                                    <select name="activate" 
                                            id="activate" 
                                            class="form-control @error('activate') is-invalid @enderror" 
                                            required>
                                        <option value="">{{ __('messages.select_status') }}</option>
                                        <option value="1" {{ old('activate', '1') == '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                        <option value="2" {{ old('activate') == '2' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                                    </select>
                                    @error('activate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                              
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('messages.create_patient') }}
                            </button>
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
</script>
@endpush

