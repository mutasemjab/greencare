@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('messages.card_numbers_for') }}: {{ $card->name }}</h4>
                    <div>
                        <a href="{{ route('cards.show', $card) }}" class="btn btn-info btn-sm">
                            {{ __('messages.card_details') }}
                        </a>
                        <a href="{{ route('cards.index') }}" class="btn btn-secondary btn-sm">
                            {{ __('messages.back_to_cards') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Card Info Summary -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-2">
                                            <h6>{{ __('messages.card_name') }}</h6>
                                            <strong>{{ $card->name }}</strong>
                                        </div>
                                        <div class="col-md-2">
                                            <h6>{{ __('messages.uses_per_card') }}</h6>
                                            <span class="badge bg-info">{{ $card->number_of_use_for_one_card }}</span>
                                        </div>
                                        <div class="col-md-1">
                                            <h6>{{ __('messages.total_numbers') }}</h6>
                                            <span class="badge bg-primary">{{ $cardNumbers->total() }}</span>
                                        </div>
                                        <div class="col-md-2">
                                            <h6>{{ __('messages.available_for_sale') }}</h6>
                                            <span class="badge bg-success">{{ $card->available_for_sale_count }}</span>
                                        </div>
                                        <div class="col-md-1">
                                            <h6>{{ __('messages.sold_not_assigned') }}</h6>
                                            <span class="badge bg-info">{{ $card->sold_not_assigned_count }}</span>
                                        </div>
                                        <div class="col-md-2">
                                            <h6>{{ __('messages.sold_assigned') }}</h6>
                                            <span class="badge bg-warning">{{ $card->sold_and_assigned_count }}</span>
                                        </div>
                                        <div class="col-md-2">
                                            <h6>{{ __('messages.fully_used') }}</h6>
                                            <span class="badge bg-danger">{{ $card->used_card_numbers_count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter and Actions -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('cards.card-numbers', $card) }}" class="d-flex">
                                <select name="status" class="form-select me-2" onchange="this.form.submit()">
                                    <option value="">{{ __('messages.all_status') }}</option>
                                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>{{ __('messages.available_for_sale') }}</option>
                                    <option value="sold_not_assigned" {{ request('status') == 'sold_not_assigned' ? 'selected' : '' }}>{{ __('messages.sold_not_assigned') }}</option>
                                    <option value="sold_assigned" {{ request('status') == 'sold_assigned' ? 'selected' : '' }}>{{ __('messages.sold_assigned') }}</option>
                                    <option value="partially_used" {{ request('status') == 'partially_used' ? 'selected' : '' }}>{{ __('messages.partially_used') }}</option>
                                    <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>{{ __('messages.fully_used') }}</option>
                                </select>
                                <select name="activate" class="form-select me-2" onchange="this.form.submit()">
                                    <option value="">{{ __('messages.all_activate') }}</option>
                                    <option value="1" {{ request('activate') == '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                    <option value="2" {{ request('activate') == '2' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                                </select>
                                <input type="text" name="search" class="form-control me-2" placeholder="{{ __('messages.search_user_or_number') }}" value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">{{ __('messages.filter') }}</button>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <form action="{{ route('cards.regenerate-numbers', $card) }}" 
                                  method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('{{ __('messages.confirm_regenerate') }}')">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-warning">
                                    {{ __('messages.regenerate_all') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($cardNumbers->count() > 0)
                        @can('cardnumbers-table')
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>{{ __('messages.id') }}</th>
                                        <th>{{ __('messages.card_number') }}</th>
                                        <th>{{ __('messages.usage_status') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th>{{ __('messages.sell_status') }}</th>
                                        <th>{{ __('messages.activate_status') }}</th>
                                        <th>{{ __('messages.created_at') }}</th>
                                        <th>{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cardNumbers as $cardNumber)
                                        <tr>
                                            <td>{{ $cardNumber->id }}</td>
                                            <td>
                                                <strong>{{ $cardNumber->number }}</strong>
                                            </td>
                                            <td>
                                                <!-- Usage Progress Bar -->
                                                <div class="mb-1">
                                                    <small>{{ __('messages.used') }}: <strong>{{ $cardNumber->usages_count }}</strong> / {{ $card->number_of_use_for_one_card }}</small>
                                                </div>
                                                <div class="progress" style="height: 20px;">
                                                    @php
                                                        $percentage = $card->number_of_use_for_one_card > 0 
                                                            ? ($cardNumber->usages_count / $card->number_of_use_for_one_card) * 100 
                                                            : 0;
                                                        $barClass = $percentage >= 100 ? 'bg-danger' : ($percentage >= 50 ? 'bg-warning' : 'bg-success');
                                                    @endphp
                                                    <div class="progress-bar {{ $barClass }}" 
                                                         role="progressbar" 
                                                         style="width: {{ min($percentage, 100) }}%"
                                                         aria-valuenow="{{ $cardNumber->usages_count }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="{{ $card->number_of_use_for_one_card }}">
                                                        {{ round($percentage, 1) }}%
                                                    </div>
                                                </div>
                                                @if($cardNumber->usages_count > 0)
                                                    <small class="text-muted">
                                                        {{ __('messages.remaining') }}: {{ max(0, $card->number_of_use_for_one_card - $cardNumber->usages_count) }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $cardNumber->getStatusBadgeClass() }}">
                                                    {{ $cardNumber->getStatusText() }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($cardNumber->sell == 1)
                                                    <span class="badge bg-info">{{ __('messages.sold') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('messages.not_sold') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($cardNumber->activate == 1)
                                                    <span class="badge bg-success">{{ __('messages.active') }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ __('messages.inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $cardNumber->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <div class="btn-group-vertical" role="group">
                                                    <!-- View Usage History Button -->
                                                    @if($cardNumber->usages_count > 0)
                                                        <a href="{{ route('card-numbers.usage-history', $cardNumber) }}" 
                                                           class="btn btn-primary btn-sm mb-1">
                                                            <i class="fas fa-history"></i> {{ __('messages.view_usage') }} ({{ $cardNumber->usages_count }})
                                                        </a>
                                                    @endif
                                                    
                                                    @can('cardnumbers-edit')
                                                        @if($cardNumber->isAvailableForSale())
                                                            <!-- Mark as Sold Button -->
                                                            <form action="{{ route('card-numbers.toggle-sell', $cardNumber) }}" 
                                                                method="POST" 
                                                                style="display: inline-block;"
                                                                onsubmit="return confirm('{{ __('messages.confirm_mark_sold') }}')">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-info btn-sm mb-1">
                                                                    {{ __('messages.mark_as_sold') }}
                                                                </button>
                                                            </form>
                                                            
                                                        @elseif($cardNumber->isSoldNotAssigned())
                                                            <!-- Mark as Not Sold Button -->
                                                            <form action="{{ route('card-numbers.toggle-sell', $cardNumber) }}" 
                                                                method="POST" 
                                                                style="display: inline-block;"
                                                                onsubmit="return confirm('{{ __('messages.confirm_mark_not_sold') }}')">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-outline-secondary btn-sm mb-1">
                                                                    {{ __('messages.mark_as_not_sold') }}
                                                                </button>
                                                            </form>
                                                            
                                                        @elseif($cardNumber->isSoldAndAssigned())
                                                            <!-- Mark as Used Button -->
                                                            <form action="{{ route('card-numbers.mark-used', $cardNumber) }}" 
                                                                method="POST" 
                                                                style="display: inline-block;"
                                                                onsubmit="return confirm('{{ __('messages.confirm_mark_used') }}')">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-success btn-sm mb-1">
                                                                    {{ __('messages.mark_as_used') }}
                                                                </button>
                                                            </form>
                                                            
                                                        @elseif($cardNumber->isUsed())
                                                            <!-- Mark as Not Used Button -->
                                                            <form action="{{ route('card-numbers.toggle-status', $cardNumber) }}" 
                                                                method="POST" 
                                                                style="display: inline-block;"
                                                                onsubmit="return confirm('{{ __('messages.confirm_mark_unused') }}')">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-outline-success btn-sm mb-1">
                                                                    {{ __('messages.mark_unused') }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endcan
                                                    
                                                    @can('cardnumbers-activate')
                                                        <!-- Toggle Activate Button -->
                                                        <form action="{{ route('card-numbers.toggle-activate', $cardNumber) }}" 
                                                            method="POST" 
                                                            style="display: inline-block;">
                                                            @csrf
                                                            @method('PATCH')
                                                            @if($cardNumber->activate == 1)
                                                                <button type="submit" class="btn btn-warning btn-sm">
                                                                    {{ __('messages.deactivate') }}
                                                                </button>
                                                            @else
                                                                <button type="submit" class="btn btn-secondary btn-sm">
                                                                    {{ __('messages.activate') }}
                                                                </button>
                                                            @endif
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endcan

                        <div class="d-flex justify-content-center">
                            {{ $cardNumbers->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center">
                            <p class="text-muted">{{ __('messages.no_card_numbers_found') }}</p>
                            <form action="{{ route('cards.regenerate-numbers', $card) }}" 
                                  method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('{{ __('messages.confirm_regenerate') }}')">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-primary">
                                    {{ __('messages.generate_numbers') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection