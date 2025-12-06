@extends('layouts.admin')

@section('title', __('messages.form_details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('special-medical-forms.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
                </a>
            </div>

            <!-- Main Form Card -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-medical"></i> {{ $specialMedicalForm->title }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">{{ __('messages.form_id') }}</th>
                                    <td>{{ $specialMedicalForm->id }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.room') }}</th>
                                    <td>
                                        <strong>{{ $specialMedicalForm->room->title }}</strong>
                                        @if($specialMedicalForm->room->description)
                                            <br><small class="text-muted">{{ $specialMedicalForm->room->description }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.created_by') }}</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($specialMedicalForm->creator->photo)
                                                <img src="{{ asset('storage/' . $specialMedicalForm->creator->photo) }}" 
                                                     alt="{{ $specialMedicalForm->creator->name }}" 
                                                     class="rounded-circle me-2" 
                                                     width="40" 
                                                     height="40">
                                            @endif
                                            <div>
                                                <div>{{ $specialMedicalForm->creator->name }}</div>
                                                <small>
                                                    @if($specialMedicalForm->creator->user_type == 'doctor')
                                                        <span class="badge bg-primary">{{ __('messages.doctor') }}</span>
                                                    @elseif($specialMedicalForm->creator->user_type == 'nurse')
                                                        <span class="badge bg-info">{{ __('messages.nurse') }}</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.status') }}</th>
                                    <td>
                                        @if($specialMedicalForm->status == 'open')
                                            <span class="badge bg-success">{{ __('messages.form_status_open') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('messages.form_status_closed') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">{{ __('messages.created_at') }}</th>
                                    <td>{{ $specialMedicalForm->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.updated_at') }}</th>
                                    <td>{{ $specialMedicalForm->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.total_replies') }}</th>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $specialMedicalForm->replies->count() }} {{ __('messages.replies') }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Main Note -->
                    <div class="mt-4">
                        <h5>{{ __('messages.note') }}:</h5>
                        <div class="border p-3 rounded bg-light">
                            {{ $specialMedicalForm->note }}
                        </div>
                    </div>

                    <!-- Signature -->
                    <div class="mt-4">
                        <h5>{{ __('messages.signature') }}:</h5>
                        <div class="border p-3 rounded bg-light text-center">
                            <img src="{{ $specialMedicalForm->signature_url }}" 
                                 alt="{{ __('messages.signature') }}" 
                                 style="max-width: 300px; max-height: 150px;"
                                 class="img-thumbnail">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Replies Section -->
            @if($specialMedicalForm->replies->count() > 0)
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-comments"></i> {{ __('messages.replies') }} ({{ $specialMedicalForm->replies->count() }})
                        </h4>
                    </div>
                    <div class="card-body">
                        @foreach($specialMedicalForm->replies as $index => $reply)
                            <div class="card mb-3 {{ $index % 2 == 0 ? 'border-primary' : 'border-info' }}">
                                <div class="card-header {{ $index % 2 == 0 ? 'bg-primary' : 'bg-info' }} text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            @if($reply->user->photo)
                                                <img src="{{ asset('storage/' . $reply->user->photo) }}" 
                                                     alt="{{ $reply->user->name }}" 
                                                     class="rounded-circle me-2" 
                                                     width="35" 
                                                     height="35">
                                            @endif
                                            <div>
                                                <strong>{{ $reply->user->name }}</strong>
                                                @if($reply->user->user_type == 'doctor')
                                                    <span class="badge bg-light text-dark ms-2">{{ __('messages.doctor') }}</span>
                                                @elseif($reply->user->user_type == 'nurse')
                                                    <span class="badge bg-light text-dark ms-2">{{ __('messages.nurse') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <small>{{ $reply->created_at->format('Y-m-d H:i:s') }}</small>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6>{{ __('messages.note') }}:</h6>
                                            <div class="border p-3 rounded bg-light">
                                                {{ $reply->note }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6>{{ __('messages.signature') }}:</h6>
                                            <div class="border p-2 rounded bg-light text-center">
                                                <img src="{{ $reply->signature_url }}" 
                                                     alt="{{ __('messages.signature') }}" 
                                                     style="max-width: 200px; max-height: 100px;"
                                                     class="img-thumbnail">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="card mt-4">
                    <div class="card-body text-center text-muted">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>{{ __('messages.no_replies_yet') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection