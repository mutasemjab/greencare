@extends('layouts.admin')

@section('title', __('messages.edit_appointment'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    {{ __('messages.edit_appointment') }}
                </h2>
                <a href="{{ route('showers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right me-2"></i>
                    {{ __('messages.back') }}
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('showers.update', $shower->id) }}" method="POST" id="showerForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- User Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">
                                    {{ __('messages.user') }} <span class="text-danger">*</span>
                                </label>
                                <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                    <option value="">{{ __('messages.select_user') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                                {{ (old('user_id', $shower->user_id) == $user->id) ? 'selected' : '' }}>
                                            {{ $user->name }} - {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Patient Code -->
                            <div class="col-md-6 mb-3">
                                <label for="code_patient" class="form-label">{{ __('messages.patient_code') }}</label>
                                <select name="code_patient" id="code_patient" class="form-control @error('code_patient') is-invalid @enderror">
                                    <option value="">{{ __('messages.select_room') }}</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->code }}" 
                                                data-discount="{{ $room->discount }}"
                                                {{ (old('code_patient', $shower->code_patient) == $room->code) ? 'selected' : '' }}>
                                            {{ $room->title }} - {{ $room->code }}
                                            @if($room->discount > 0)
                                                ({{ __('messages.discount') }} {{ $room->discount }}%)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('code_patient')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">{{ __('messages.if_patient_in_room') }}</small>
                            </div>

                            <!-- Date -->
                            <div class="col-md-6 mb-3">
                                <label for="date_of_shower" class="form-label">
                                    {{ __('messages.date_of_shower') }} <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="date_of_shower" 
                                       id="date_of_shower" 
                                       class="form-control @error('date_of_shower') is-invalid @enderror" 
                                       value="{{ old('date_of_shower', $shower->date_of_shower->format('Y-m-d')) }}"
                                       required>
                                @error('date_of_shower')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Time -->
                            <div class="col-md-6 mb-3">
                                <label for="time_of_shower" class="form-label">{{ __('messages.time_of_shower') }}</label>
                                <input type="time" 
                                       name="time_of_shower" 
                                       id="time_of_shower" 
                                       class="form-control @error('time_of_shower') is-invalid @enderror" 
                                       value="{{ old('time_of_shower', $shower->time_of_shower ? $shower->time_of_shower->format('H:i') : '') }}">
                                @error('time_of_shower')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Payment Method Selection -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    {{ __('messages.payment_method') }} <span class="text-danger">*</span>
                                </label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="payment_type" id="payment_cash" value="cash" 
                                           {{ !$shower->card_number_id ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success" for="payment_cash">
                                        <i class="fas fa-money-bill-wave me-2"></i>{{ __('messages.cash') }}
                                    </label>

                                    <input type="radio" class="btn-check" name="payment_type" id="payment_card" value="card"
                                           {{ $shower->card_number_id ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="payment_card">
                                        <i class="fas fa-credit-card me-2"></i>{{ __('messages.card') }}
                                    </label>
                                </div>
                            </div>

                            <!-- Price (for cash payment) -->
                            <div class="col-md-6 mb-3" id="price_field" style="{{ $shower->card_number_id ? 'display: none;' : '' }}">
                                <label for="price" class="form-label">
                                    {{ __('messages.price') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           name="price" 
                                           id="price" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           value="{{ old('price', $shower->price) }}"
                                           step="0.01"
                                           min="0"
                                           {{ !$shower->card_number_id ? 'required' : '' }}>
                                    <span class="input-group-text">{{ __('messages.currency') }}</span>
                                </div>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">{{ __('messages.default_price') }}: {{ $defaultPrice }}</small>
                            </div>

                            <!-- Card Selection (for card payment) -->
                            <div class="col-md-6 mb-3" id="card_field" style="{{ !$shower->card_number_id ? 'display: none;' : '' }}">
                                <label for="card_number_id" class="form-label">{{ __('messages.select_card') }}</label>
                                <select name="card_number_id" id="card_number_id" class="form-control @error('card_number_id') is-invalid @enderror">
                                    <option value="">{{ __('messages.select_card') }}</option>
                                    @foreach($availableCards as $card)
                                        <option value="{{ $card->id }}" 
                                                data-price="{{ $card->card->price }}"
                                                data-name="{{ $card->card->name }}"
                                                {{ (old('card_number_id', $shower->card_number_id) == $card->id) ? 'selected' : '' }}>
                                            {{ $card->number }} - {{ $card->card->name }} ({{ number_format($card->card->price, 2) }} {{ __('messages.currency') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('card_number_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="card_info" class="alert alert-warning mt-2" style="display: none;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="card_info_text"></span>
                                </div>
                            </div>

                            <!-- Note -->
                            <div class="col-md-12 mb-3">
                                <label for="note" class="form-label">{{ __('messages.notes') }}</label>
                                <textarea name="note" 
                                          id="note" 
                                          rows="3" 
                                          class="form-control @error('note') is-invalid @enderror" 
                                          placeholder="{{ __('messages.add_notes') }}">{{ old('note', $shower->note) }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-md-12">
                                <hr>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>{{ __('messages.update_appointment') }}
                                </button>
                                <a href="{{ route('showers.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>{{ __('messages.cancel') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentCash = document.getElementById('payment_cash');
    const paymentCard = document.getElementById('payment_card');
    const priceField = document.getElementById('price_field');
    const cardField = document.getElementById('card_field');
    const priceInput = document.getElementById('price');
    const cardSelect = document.getElementById('card_number_id');
    const cardInfo = document.getElementById('card_info');
    const cardInfoText = document.getElementById('card_info_text');
    const defaultPrice = {{ $defaultPrice }};
    const currency = '{{ __("messages.currency") }}';

    // Toggle payment method fields
    function togglePaymentFields() {
        if (paymentCard.checked) {
            priceField.style.display = 'none';
            cardField.style.display = 'block';
            priceInput.removeAttribute('required');
        } else {
            priceField.style.display = 'block';
            cardField.style.display = 'none';
            priceInput.setAttribute('required', 'required');
            cardSelect.value = '';
            cardInfo.style.display = 'none';
        }
    }

    paymentCash.addEventListener('change', togglePaymentFields);
    paymentCard.addEventListener('change', togglePaymentFields);

    // Handle card selection
    cardSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const cardPrice = selectedOption.dataset.price;
            const cardName = selectedOption.dataset.name;
            
            cardInfo.style.display = 'block';
            cardInfoText.innerHTML = `{{ __('messages.card_price') }}: <strong>${parseFloat(cardPrice).toFixed(2)} ${currency}</strong> - ${cardName}`;
            
            priceInput.value = cardPrice;
        } else {
            cardInfo.style.display = 'none';
            priceInput.value = '';
        }
    });

    // Initialize on page load
    togglePaymentFields();
    
    // Show card info if card is already selected
    if (cardSelect.value) {
        cardSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush