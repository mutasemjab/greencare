@extends('layouts.admin')

@section('title', __('messages.pledge_form_details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        {{ __('messages.pledge_form_details') }} - 
                        <span class="badge bg-{{ $pledgeForm->type == 'pledge_form' ? 'primary' : 'success' }}">
                            {{ __('messages.' . $pledgeForm->type) }}
                        </span>
                    </h3>
                    <a href="{{ route('pledge-forms.index') }}" class="btn btn-secondary">
                        {{ __('messages.back_to_list') }}
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h5>{{ __('messages.basic_information') }}</h5>
                            <table class="table table-striped">
                                <tr>
                                    <th>{{ __('messages.nurse_name') }}:</th>
                                    <td>{{ $pledgeForm->name_of_nurse }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.patient_name') }}:</th>
                                    <td>{{ $pledgeForm->name_of_patient }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.patient_identity') }}:</th>
                                    <td>{{ $pledgeForm->identity_number_of_patient }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.patient_phone') }}:</th>
                                    <td>{{ $pledgeForm->phone_of_patient ?? __('messages.not_available') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.professional_license') }}:</th>
                                    <td>{{ $pledgeForm->professional_license_number ?? __('messages.not_available') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.pledge_date') }}:</th>
                                    <td>{{ $pledgeForm->date_of_pledge ? $pledgeForm->date_of_pledge->format('Y-m-d') : __('messages.not_available') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.room') }}:</th>
                                    <td>{{ $pledgeForm->room->name ?? __('messages.not_available') }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Authorization Form Specific Fields -->
                        @if($pledgeForm->isAuthorizationForm())
                        <div class="col-md-6">
                            <h5>{{ __('messages.authorization_details') }}</h5>
                            <table class="table table-striped">
                                <tr>
                                    <th>{{ __('messages.place') }}:</th>
                                    <td>{{ $pledgeForm->place ?? __('messages.not_available') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.date_of_birth') }}:</th>
                                    <td>{{ $pledgeForm->date_of_birth ? $pledgeForm->date_of_birth->format('Y-m-d') : __('messages.not_available') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.parent_name') }}:</th>
                                    <td>{{ $pledgeForm->parent_of_patient ?? __('messages.not_available') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.parent_identity') }}:</th>
                                    <td>{{ $pledgeForm->identity_number_for_parent_of_patient ?? __('messages.not_available') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.parent_phone') }}:</th>
                                    <td>{{ $pledgeForm->phone_for_parent_of_patient ?? __('messages.not_available') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.kinship') }}:</th>
                                    <td>{{ $pledgeForm->kinship ?? __('messages.not_available') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.commissioner_name') }}:</th>
                                    <td>{{ $pledgeForm->full_name_of_commissioner ?? __('messages.not_available') }}</td>
                                </tr>
                            </table>
                        </div>
                        @endif
                    </div>

                    <!-- Pledge Text -->
                    @if($pledgeForm->pledge_text)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>{{ __('messages.pledge_text') }}</h5>
                            <div class="card">
                                <div class="card-body">
                                    {{ $pledgeForm->pledge_text }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Signatures -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>{{ __('messages.signatures') }}</h5>
                            <div class="row">
                                @for($i = 1; $i <= 4; $i++)
                                    @php
                                        $signatureField = 'signature_' . ($i == 1 ? 'one' : ($i == 2 ? 'two' : ($i == 3 ? 'three' : 'four')));
                                        $signature = $pledgeForm->$signatureField;
                                    @endphp
                                    <div class="col-md-3 mb-3">
                                        <div class="card">
                                            <div class="card-header text-center">
                                                <strong>{{ __('messages.signature') }} {{ $i }}</strong>
                                            </div>
                                            <div class="card-body text-center">
                                                @if($signature)
                                                    <img src="{{ asset('assets/admin/uploads/' . $signature) }}" 
                                                         alt="{{ __('messages.signature') }} {{ $i }}" 
                                                         class="img-fluid" 
                                                         style="max-height: 150px;">
                                                @else
                                                    <p class="text-muted">{{ __('messages.not_available') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p><strong>{{ __('messages.created_at') }}:</strong> {{ $pledgeForm->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('messages.updated_at') }}:</strong> {{ $pledgeForm->updated_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection