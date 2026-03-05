@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('messages.assign_card_to_user') }}</h4>
                    <a href="{{ url()->previous() }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_cards') }}
                    </a>
                </div>

                <div class="card-body">
                    <!-- Card Number Info -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-6">
                                <strong>{{ __('messages.card_number') }}:</strong>
                                <p class="mb-0 text-muted">{{ $cardNumber->number }}</p>
                            </div>
                            <div class="col-6">
                                <strong>{{ __('messages.status') }}:</strong>
                                <p class="mb-0">
                                    <span class="badge {{ $cardNumber->getStatusBadgeClass() }}">
                                        {{ $cardNumber->getStatusText() }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Assign Form -->
                    <form action="{{ route('card-numbers.assign', $cardNumber) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="user_id" class="form-label">
                                <strong>{{ __('messages.select_user') }}</strong>
                            </label>
                            <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">-- {{ __('messages.select_user') }} --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @if(old('user_id') == $user->id) selected @endif>
                                        {{ $user->name }} @if($user->email)
                                            ({{ $user->email }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Current Assignment Info -->
                        @if($cardNumber->assigned_user_id)
                            <div class="alert alert-warning mb-3">
                                <strong>{{ __('messages.assigned_user') }}:</strong>
                                <p class="mb-0">{{ $cardNumber->assignedUser?->name }}</p>
                            </div>
                        @endif

                        <!-- Form Actions -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                {{ __('messages.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> {{ __('messages.assign') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
