@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('messages.pos_details') }}</h3>
                        <div>
                            @can('pos-edit')
                                <a href="{{ route('pos.edit', $po) }}" class="btn btn-warning btn-sm me-2">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                            @endcan
                            <a href="{{ route('pos.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- POS Info -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle"></i> {{ __('messages.pos_information') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.name') }}:</strong></td>
                                            <td>{{ $po->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.phone') }}:</strong></td>
                                            <td>{{ $po->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.address') }}:</strong></td>
                                            <td>{{ $po->address }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.country') }}:</strong></td>
                                            <td>{{ $po->country_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.google_map_link') }}:</strong></td>
                                            <td>
                                                @if($po->google_map_link)
                                                    <a href="{{ $po->google_map_link }}" target="_blank" class="text-decoration-none">
                                                        <i class="fas fa-map-marker-alt"></i> {{ __('messages.view_on_map') }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('messages.total_cards') }}:</strong></td>
                                            <td>
                                                <span class="badge bg-info">{{ $po->total_cards }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.created_at') }}:</strong></td>
                                            <td>{{ $po->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ __('messages.updated_at') }}:</strong></td>
                                            <td>{{ $po->updated_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cards -->
                    @if($po->cards->count() > 0)
                        <div class="card mt-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card"></i> {{ __('messages.cards') }}
                                    <span class="badge bg-light text-success ms-2">{{ $po->cards->count() }}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('messages.id') }}</th>
                                                <th>{{ __('messages.name') }}</th>
                                                <th>{{ __('messages.card_numbers_count') }}</th>
                                                <th>{{ __('messages.created_at') }}</th>
                                                <th>{{ __('messages.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($po->cards as $card)
                                                <tr>
                                                    <td>{{ $card->id }}</td>
                                                    <td>{{ $card->name ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            {{ $card->cardNumbers ? $card->cardNumbers->count() : 0 }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $card->created_at->format('Y-m-d H:i') }}</td>
                                                    <td>
                                                        @can('card-view')
                                                            <a href="{{ route('cards.show', $card) }}" class="btn btn-sm btn-outline-primary">
                                                                {{ __('messages.view') }}
                                                            </a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle"></i> {{ __('messages.no_cards_found') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
