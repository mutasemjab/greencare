@extends('layouts.admin')

@section('title', __('messages.invoice'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4>{{ __('messages.invoice') }} #{{ $order->number }}</h4>
                <div>
                    <button onclick="window.print()" class="btn btn-primary btn-sm me-2">
                        <i class="fas fa-print"></i> {{ __('messages.print') }}
                    </button>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Content -->
    <div class="row">
        <div class="col-12">
            <div class="card" style="border: 1px solid #ddd;">
                <div class="card-body">
                    <!-- Invoice Header -->
                    <div class="row mb-5">
                        <div class="col-6">
                            <h3 class="mb-1">{{ __('messages.invoice') }}</h3>
                            <p class="text-muted mb-0">{{ __('messages.invoice_number') }}: <strong>#{{ $order->number }}</strong></p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-1">{{ __('messages.order_date') }}: <strong>{{ $order->date->format('Y-m-d') }}</strong></p>
                            <p class="text-muted mb-0">{{ __('messages.time') }}: {{ $order->date->format('H:i:s') }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Customer Information -->
                    <div class="row mb-5">
                        <div class="col-6">
                            <h6 class="mb-2">{{ __('messages.bill_to') }}:</h6>
                            @if($order->user)
                                <p class="mb-1"><strong>{{ $order->user->name }}</strong></p>
                                <p class="mb-1 text-muted">{{ $order->user->email }}</p>
                                <p class="text-muted">{{ $order->user->phone ?? '-' }}</p>
                            @else
                                <p class="text-muted">-</p>
                            @endif
                        </div>
                        <div class="col-6">
                            <h6 class="mb-2">{{ __('messages.shipping_address') }}:</h6>
                            @if($order->address)
                                <p class="mb-0 text-muted">
                                    {{ $order->address->address ?? '-' }}
                                </p>
                            @else
                                <p class="text-muted">-</p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <!-- Order Items Table -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">{{ __('messages.no') }}</th>
                                            <th width="40%">{{ __('messages.product') }}</th>
                                            <th width="10%" class="text-center">{{ __('messages.quantity') }}</th>
                                            <th width="15%" class="text-end">{{ __('messages.unit_price') }}</th>
                                            <th width="15%" class="text-end">{{ __('messages.total') }}</th>
                                            <th width="15%" class="text-end">{{ __('messages.after_tax') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $itemCount = 1; @endphp
                                        @forelse($order->orderProducts as $item)
                                            <tr>
                                                <td>{{ $itemCount++ }}</td>
                                                <td>
                                                    <strong>{{ $item->product->name ?? '-' }}</strong>
                                                    @if($item->discount_percentage)
                                                        <br><small class="text-danger">{{ __('messages.discount') }}: {{ $item->discount_percentage }}% (-{{ number_format($item->discount_value, 2) }})</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                                <td class="text-end">{{ number_format($item->total_price_before_tax, 2) }}</td>
                                                <td class="text-end"><strong>{{ number_format($item->total_price_after_tax, 2) }}</strong></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">{{ __('messages.no_items') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="row">
                        <div class="col-6"></div>
                        <div class="col-6">
                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <td width="60%"><strong>{{ __('messages.subtotal') }}:</strong></td>
                                        <td class="text-end"><strong>{{ number_format($order->total_prices, 2) }}</strong></td>
                                    </tr>
                                    @if($order->total_discounts > 0)
                                    <tr class="text-danger">
                                        <td><strong>{{ __('messages.discounts') }}:</strong></td>
                                        <td class="text-end"><strong>-{{ number_format($order->total_discounts, 2) }}</strong></td>
                                    </tr>
                                    @endif
                                    @if($order->coupon_discount > 0)
                                    <tr class="text-danger">
                                        <td><strong>{{ __('messages.coupon_discount') }}:</strong></td>
                                        <td class="text-end"><strong>-{{ number_format($order->coupon_discount, 2) }}</strong></td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>{{ __('messages.taxes') }}:</strong></td>
                                        <td class="text-end"><strong>{{ number_format($order->total_taxes, 2) }}</strong></td>
                                    </tr>
                                    @if($order->delivery_fee > 0)
                                    <tr>
                                        <td><strong>{{ __('messages.delivery_fee') }}:</strong></td>
                                        <td class="text-end"><strong>{{ number_format($order->delivery_fee, 2) }}</strong></td>
                                    </tr>
                                    @endif
                                    <tr class="border-top-2">
                                        <td><h6 class="mb-0">{{ __('messages.total') }}:</h6></td>
                                        <td class="text-end"><h6 class="mb-0">{{ number_format(($order->total_prices - $order->total_discounts - ($order->coupon_discount ?? 0) + $order->total_taxes + $order->delivery_fee), 2) }}</h6></td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('messages.payment_status') }}:</strong></td>
                                        <td class="text-end">
                                            <span class="badge {{ $order->payment_status == 1 ? 'bg-success' : 'bg-warning' }}">
                                                {{ $order->payment_status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Notes -->
                    @if($order->note)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="mb-2">{{ __('messages.notes') }}:</h6>
                            <p class="text-muted">{{ $order->note }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Footer -->
                    <div class="row mt-5 pt-3 border-top">
                        <div class="col-12 text-center text-muted">
                            <small>
                                {{ __('messages.thank_you_for_your_business') }}
                                <br>
                                {{ config('app.name') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style media="print">
    @page {
        size: A4;
        margin: 0;
    }
    body {
        margin: 0;
        padding: 10mm;
    }
    .btn-group,
    .btn {
        display: none !important;
    }
</style>
@endsection
