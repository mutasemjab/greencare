@extends('layouts.admin')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('messages.edit_card') }}</h4>
                    <a href="{{ route('cards.index') }}" class="btn btn-secondary">
                        {{ __('messages.back_to_list') }}
                    </a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('cards.update', $card) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="pos_id" class="form-label">{{ __('messages.pos') }}</label>
                            <select class="form-control @error('pos_id') is-invalid @enderror" 
                                    id="pos_id" 
                                    name="pos_id">
                                <option value="">{{ __('messages.select_pos') }}</option>
                                @foreach($posRecords as $pos)
                                    <option value="{{ $pos->id }}" 
                                            {{ old('pos_id', $card->pos_id) == $pos->id ? 'selected' : '' }}>
                                        {{ $pos->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pos_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $card->name) }}" 
                                   placeholder="{{ __('messages.enter_card_name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">{{ __('messages.price') }} <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('price') is-invalid @enderror" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price', $card->price) }}" 
                                   placeholder="{{ __('messages.enter_price') }}"
                                   step="any"
                                   min="0"
                                   required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">{{ __('messages.selling_price') }} <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('selling_price') is-invalid @enderror" 
                                   id="selling_price" 
                                   name="selling_price" 
                                   value="{{ old('selling_price', $card->selling_price) }}" 
                                   placeholder="{{ __('messages.enter_selling_price') }}"
                                   step="any"
                                   min="0"
                                   required>
                            @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label">{{ __('messages.photo') }}</label>
                            @if($card->photo)
                                <div class="mb-2">
                                    <img src="{{ $card->photo_url }}" alt="{{ $card->name }}" 
                                         class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                    <p class="form-text">{{ __('messages.current_photo') }}</p>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" 
                                   id="photo" name="photo" accept="image/*">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('messages.photo_requirements') }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="number_of_cards" class="form-label">{{ __('messages.number_of_cards') }} <span class="text-danger">*</span></label>
                            <input type="number" 
                                   class="form-control @error('number_of_cards') is-invalid @enderror" 
                                   id="number_of_cards" 
                                   name="number_of_cards" 
                                   value="{{ old('number_of_cards', $card->number_of_cards) }}" 
                                   placeholder="{{ __('messages.enter_number_of_cards') }}"
                                   min="1"
                                   max="10000"
                                   required>
                            @error('number_of_cards')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('messages.number_of_cards_help') }}</div>
                        </div>

                        @if($card->number_of_cards != old('number_of_cards', $card->number_of_cards))
                            <div class="alert alert-warning">
                                <strong>{{ __('messages.warning') }}:</strong> {{ __('messages.number_change_warning') }}
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <strong>{{ __('messages.current_stats') }}:</strong><br>
                            {{ __('messages.current_card_numbers') }}: {{ $card->cardNumbers->count() }}<br>
                            {{ __('messages.active_numbers') }}: {{ $card->active_card_numbers_count }}<br>
                            {{ __('messages.used_numbers') }}: {{ $card->used_card_numbers_count }}
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('cards.index') }}" class="btn btn-secondary me-md-2">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: '{{ __("messages.select_doseyats") }}',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush