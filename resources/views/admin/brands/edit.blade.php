@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.Edit_Brand') }}</h3>
                    <a href="{{ route('brands.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('messages.Back') }}
                    </a>
                </div>

                <form action="{{ route('brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
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
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ __('messages.Brand_Information') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('messages.Name_English') }} <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('name_en') is-invalid @enderror" 
                                                           name="name_en" value="{{ old('name_en', $brand->name_en) }}" required>
                                                    @error('name_en')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('messages.Name_Arabic') }} <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror" 
                                                           name="name_ar" value="{{ old('name_ar', $brand->name_ar) }}" required>
                                                    @error('name_ar')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>{{ __('messages.Brand_Photo') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Current Photo -->
                                        @if($brand->photo)
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('messages.Current_Photo') }}:</label>
                                                <div class="text-center">
                                                    <img src="{{ asset('assets/admin/uploads/'.$brand->photo) }}" 
                                                         alt="{{ $brand->name_en }}" 
                                                         class="img-fluid rounded border" 
                                                         style="width: 100%; height: 200px; object-fit: cover;">
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <label class="form-label">
                                                {{ $brand->photo ? __('messages.Change_Photo') : __('messages.Photo') }}
                                                @if(!$brand->photo)<span class="text-danger">*</span>@endif
                                            </label>
                                            <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                                   name="photo" accept="image/*" {{ !$brand->photo ? 'required' : '' }}>
                                            <small class="form-text text-muted">
                                                {{ __('messages.Allowed_Formats') }}: JPG, JPEG, PNG, GIF
                                            </small>
                                            @error('photo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- New Image Preview -->
                                        <div id="imagePreview" class="mt-3" style="display: none;">
                                            <label class="form-label">{{ __('messages.New_Preview') }}:</label>
                                            <img id="previewImg" src="" alt="Preview" 
                                                 class="img-fluid rounded border" 
                                                 style="width: 100%; height: 200px; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('messages.Update_Brand') }}
                        </button>
                        <a href="{{ route('brands.index') }}" class="btn btn-secondary">
                            {{ __('messages.Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.querySelector('input[name="photo"]');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.style.display = 'none';
        }
    });
});
</script>
@endpush