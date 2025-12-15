@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('messages.usage_history') }}</h4>
                    <a href="{{ route('cards.card-numbers', $cardNumber->card) }}" class="btn btn-secondary btn-sm">
                        {{ __('messages.back') }}
                    </a>
                </div>

                <div class="card-body">
                    <!-- Card Number Info -->
                    <div class="card border-info mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <h6>{{ __('messages.card_number') }}</h6>
                                    <strong>{{ $cardNumber->number }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <h6>{{ __('messages.card_name') }}</h6>
                                    <strong>{{ $cardNumber->card->name }}</strong>
                                </div>
                                <div class="col-md-2">
                                    <h6>{{ __('messages.total_uses') }}</h6>
                                    <span class="badge bg-primary">{{ $cardNumber->usages->count() }} / {{ $cardNumber->card->number_of_use_for_one_card }}</span>
                                </div>
                                <div class="col-md-2">
                                    <h6>{{ __('messages.remaining_uses') }}</h6>
                                    <span class="badge bg-success">{{ max(0, $cardNumber->card->number_of_use_for_one_card - $cardNumber->usages->count()) }}</span>
                                </div>
                                <div class="col-md-2">
                                    <h6>{{ __('messages.status') }}</h6>
                                    <span class="badge {{ $cardNumber->getStatusBadgeClass() }}">
                                        {{ $cardNumber->getStatusText() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Usage History Table -->
                    @if($usages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('messages.user') }}</th>
                                        <th>{{ __('messages.used_at') }}</th>
                                        <th>{{ __('messages.time_ago') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usages as $index => $usage)
                                        <tr>
                                            <td>{{ $usages->firstItem() + $index }}</td>
                                            <td>
                                                @if($usage->user)
                                                    <div>
                                                        <strong>{{ $usage->user->name }}</strong>
                                                    </div>
                                                    <small class="text-muted">{{ $usage->user->email }}</small>
                                                @else
                                                    <span class="text-muted">{{ __('messages.user_deleted') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $usage->used_at ? $usage->used_at->format('Y-m-d H:i:s') : '-' }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $usage->used_at ? $usage->used_at->diffForHumans() : '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $usages->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            {{ __('messages.no_usage_history') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection