@extends('layouts.admin')

@section('title', __('messages.view_appointment'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="fas fa-eye me-2"></i>
                    {{ __('messages.appointment_details') }}
                </h2>
                <div>
                    <a href="{{ route('showers.edit', $shower->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>
                        {{ __('messages.edit') }}
                    </a>
                    <a href="{{ route('showers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>
                        {{ __('messages.back') }}
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Main Information -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('messages.appointment_details') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th width="30%">{{ __('messages.id') }}:</th>
                                        <td><span class="badge bg-secondary">#{{ $shower->id }}</span></td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.user') }}:</th>
                                        <td>
                                            <div>
                                                <strong>{{ $shower->user->name }}</strong>
                                            </div>
                                            <div class="text-muted">
                                                <i class="fas fa-envelope me-1"></i>
                                                {{ $shower->user->email }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.patient_code') }}:</th>
                                        <td>
                                            @if($shower->code_patient)
                                                <span class="badge bg-info">{{ $shower->code_patient }}</span>
                                                @if($discountInfo)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ __('messages.room_title') }}: {{ $discountInfo['room']->title }}
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.date_of_shower') }}:</th>
                                        <td>
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $shower->date_of_shower->format('Y-m-d') }}
                                            <small class="text-muted">({{ $shower->date_of_shower->diffForHumans() }})</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.time_of_shower') }}:</th>
                                        <td>
                                            @if($shower->time_of_shower)
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $shower->time_of_shower->format('H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.payment_method') }}:</th>
                                        <td>
                                            @if($shower->card_number_id)
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-credit-card me-1"></i>
                                                    {{ __('messages.card') }}
                                                </span>
                                            @else
                                                <span class="badge bg-success">
                                                    <i class="fas fa-money-bill-wave me-1"></i>
                                                    {{ __('messages.cash') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.price') }}:</th>
                                        <td>
                                            <h4 class="text-success mb-0">
                                                {{ number_format($shower->price, 2) }} {{ __('messages.currency') }}
                                            </h4>
                                        </td>
                                    </tr>
                                    @if($shower->note)
                                    <tr>
                                        <th>{{ __('messages.notes') }}:</th>
                                        <td>
                                            <div class="alert alert-light mb-0">
                                                <i class="fas fa-sticky-note me-1"></i>
                                                {{ $shower->note }}
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Card Information -->
                    @if($shower->card_number_id && $shower->cardNumber)
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>
                                {{ __('messages.card_info') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th>{{ __('messages.card_number') }}:</th>
                                        <td>
                                            <span class="badge bg-dark">{{ $shower->cardNumber->number }}</span>
                                        </td>
                                    </tr>
                                    @if($shower->cardNumber->card)
                                    <tr>
                                        <th>{{ __('messages.name') }}:</th>
                                        <td>{{ $shower->cardNumber->card->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.card_price') }}:</th>
                                        <td>
                                            <strong>{{ number_format($shower->cardNumber->card->price, 2) }} {{ __('messages.currency') }}</strong>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>{{ __('messages.status') }}:</th>
                                        <td>
                                            <span class="badge bg-danger">{{ __('messages.used') }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Discount Information -->
                    @if($discountInfo && $discountInfo['discount_percentage'] > 0)
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-tag me-2"></i>
                                {{ __('messages.discount_info') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th>{{ __('messages.room_title') }}:</th>
                                        <td>{{ $discountInfo['room']->title }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.discount_percentage') }}:</th>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                {{ $discountInfo['discount_percentage'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-clock me-2"></i>
                                {{ __('messages.date_and_time') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th>{{ __('messages.created_at') }}:</th>
                                        <td>
                                            <small>
                                                {{ $shower->created_at->format('Y-m-d H:i:s') }}
                                                <br>
                                                <span class="text-muted">{{ $shower->created_at->diffForHumans() }}</span>
                                            </small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('messages.updated_at') }}:</th>
                                        <td>
                                            <small>
                                                {{ $shower->updated_at->format('Y-m-d H:i:s') }}
                                                <br>
                                                <span class="text-muted">{{ $shower->updated_at->diffForHumans() }}</span>
                                            </small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('showers.edit', $shower->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-2"></i>
                                        {{ __('messages.edit') }}
                                    </a>
                                </div>
                                <div>
                                    <form action="{{ route('showers.destroy', $shower->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash me-2"></i>
                                            {{ __('messages.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection