@extends('layouts.admin')

@section('css')
<style>
/* ── screen styles ─────────────────────────────────────── */
.form-print-wrap {
    max-width: 860px;
    margin: 0 auto;
}
.q-label {
    font-size: .78rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 3px;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.q-answer {
    background: #f8f9fa;
    border-radius: .3rem;
    padding: .45rem .8rem;
    min-height: 38px;
    font-weight: 500;
    display: flex;
    align-items: center;
    word-break: break-word;
}
.q-answer.empty-answer {
    color: #adb5bd;
    font-style: italic;
}
.sig-img {
    max-height: 90px;
    border: 1px solid #dee2e6;
    border-radius: .25rem;
    object-fit: contain;
    background: #fff;
    padding: 4px;
}
.section-divider {
    border: none;
    border-top: 2px dashed #dee2e6;
    margin: 1.5rem 0;
}
.static-text-box {
    background: #fffbf0;
    border: 1px solid #ffc107;
    border-radius: .4rem;
    padding: 1rem 1.2rem;
    font-size: .92rem;
    line-height: 1.8;
    white-space: pre-line;
}
.pledge-color  { color: #6f42c1; }
.auth-color    { color: #fd7e14; }
.pledge-border { border-color: #6f42c1 !important; }
.auth-border   { border-color: #fd7e14 !important; }
.pledge-header { background-color: #6f42c1 !important; }
.auth-header   { background-color: #fd7e14 !important; }

/* ── print styles ──────────────────────────────────────── */
@media print {
    .main-header,
    .main-sidebar,
    .main-footer,
    .content-header,
    .btn,
    .no-print {
        display: none !important;
    }

    body, .wrapper, .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        font-size: 11pt;
    }

    .content { padding: 0 !important; }

    .print-header {
        display: block !important;
        text-align: center;
        border-bottom: 2px solid #000;
        padding-bottom: 8pt;
        margin-bottom: 14pt;
    }
    .print-header h2 { margin: 0; font-size: 15pt; font-weight: bold; }
    .print-header p  { margin: 2pt 0; font-size: 9pt; }

    .print-footer {
        display: block !important;
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: center;
        font-size: 8pt;
        color: #666;
        border-top: 1px solid #ccc;
        padding-top: 4pt;
    }

    .form-print-wrap { max-width: 100%; }

    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        break-inside: avoid;
    }
    .card-header { background: #eee !important; color: #000 !important; }

    .q-answer {
        background: transparent !important;
        border-bottom: 1px dotted #888;
        border-radius: 0 !important;
        min-height: auto !important;
        padding: 2pt 4pt !important;
    }

    .sig-img {
        max-height: 70pt !important;
    }

    .static-text-box {
        background: transparent !important;
        border: 1px solid #ccc !important;
    }

    .section-divider {
        border-top: 1px solid #ccc;
    }

    a { color: #000 !important; text-decoration: none !important; }
}

/* hidden on screen */
.print-header,
.print-footer {
    display: none;
}
</style>
@endsection

@section('content')
@php
    $isPledge = $pledgeForm->isPledgeForm();
    $colorClass  = $isPledge ? 'pledge-color'  : 'auth-color';
    $borderClass = $isPledge ? 'pledge-border' : 'auth-border';
    $headerClass = $isPledge ? 'pledge-header' : 'auth-header';
    $formTitle   = $isPledge
        ? __('messages.form_type_pledge_form')
        : __('messages.form_type_authorization_form');
    $formIcon    = $isPledge ? 'fa-file-signature' : 'fa-file-contract';
@endphp

{{-- ── Print header (hidden on screen) ── --}}
<div class="print-header">
    <h2>GreenCare – {{ $formTitle }}</h2>
    <p>
        <strong>{{ __('messages.room') }}:</strong> {{ $room->title }}
        &nbsp;|&nbsp;
        <strong>{{ __('messages.date') }}:</strong> {{ now()->format('Y-m-d') }}
    </p>
    @if($pledgeForm->nurse)
        <p>
            <strong>{{ __('messages.nurse') }}:</strong>
            {{ $pledgeForm->nurse->name }}
        </p>
    @endif
</div>

<div class="print-footer">
    GreenCare &nbsp;|&nbsp; {{ $room->title }} &nbsp;|&nbsp; {{ now()->format('Y-m-d H:i') }}
</div>

{{-- ── Screen top bar ── --}}
<div class="container-fluid">
<div class="form-print-wrap">

    <div class="row mb-3 no-print">
        <div class="col-12">
            <div class="card">
                <div class="card-header {{ $headerClass }} text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas {{ $formIcon }} mr-1"></i> {{ $formTitle }}
                        <small class="ml-2 opacity-75">– {{ $room->title }}</small>
                    </h5>
                    <div>
                        <a href="{{ route('rooms.show', $room) }}"
                           class="btn btn-light btn-sm mr-2">
                            <i class="fas fa-arrow-right ml-1"></i> {{ __('messages.back') }}
                        </a>
                        <button onclick="window.print()" class="btn btn-dark btn-sm">
                            <i class="fas fa-print ml-1"></i> {{ __('messages.print') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         PLEDGE FORM
    ════════════════════════════════════════════════ --}}
    @if($isPledge)

        {{-- Section 1: Nurse info --}}
        <div class="card mb-3 border {{ $borderClass }}">
            <div class="card-header {{ $headerClass }} text-white py-2">
                <i class="fas fa-user-nurse mr-1"></i>
                {{ __('messages.nurse_information') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.pledge_nurse_name') }}</div>
                        <div class="q-answer {{ !$pledgeForm->name_of_nurse ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->name_of_nurse ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.pledge_national_number') }}</div>
                        <div class="q-answer {{ !$pledgeForm->identity_number_of_patient ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->identity_number_of_patient ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.pledge_professional_license_number') }}</div>
                        <div class="q-answer {{ !$pledgeForm->professional_license_number ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->professional_license_number ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.pledge_phone_number') }}</div>
                        <div class="q-answer {{ !$pledgeForm->phone_of_patient ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->phone_of_patient ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Patient info --}}
        <div class="card mb-3 border {{ $borderClass }}">
            <div class="card-header {{ $headerClass }} text-white py-2">
                <i class="fas fa-procedures mr-1"></i>
                {{ __('messages.patient_information') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.pledge_patient_name') }}</div>
                        <div class="q-answer {{ !$pledgeForm->name_of_patient ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->name_of_patient ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.pledge_date') }}</div>
                        <div class="q-answer {{ !$pledgeForm->date_of_pledge ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->date_of_pledge ? $pledgeForm->date_of_pledge->format('Y-m-d') : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Pledge statement (static) --}}
        <div class="card mb-3">
            <div class="card-header bg-light py-2">
                <i class="fas fa-scroll mr-1 {{ $colorClass }}"></i>
                <strong>{{ __('messages.pledge_form_pledge_statement') }}</strong>
            </div>
            <div class="card-body">
                <div class="static-text-box">
                    {{ __('messages.pledge_form_pledge_content') }}
                </div>
            </div>
        </div>

        {{-- Section 4: Signatures --}}
        <div class="card mb-3 border {{ $borderClass }}">
            <div class="card-header {{ $headerClass }} text-white py-2">
                <i class="fas fa-pen-nib mr-1"></i>
                {{ __('messages.signatures') }}
            </div>
            <div class="card-body">
                <div class="row">
                    @if($pledgeForm->signature_one)
                        <div class="col-md-6 mb-3 text-center">
                            <div class="q-label mb-2">{{ __('messages.pledge_nurse_signature') }}</div>
                            <img src="{{ asset('assets/admin/uploads/' . $pledgeForm->signature_one) }}"
                                 class="sig-img d-block mx-auto">
                        </div>
                    @endif
                    @if($pledgeForm->signature_two)
                        <div class="col-md-6 mb-3 text-center">
                            <div class="q-label mb-2">{{ __('messages.pledge_technical_manager_signature') }}</div>
                            <img src="{{ asset('assets/admin/uploads/' . $pledgeForm->signature_two) }}"
                                 class="sig-img d-block mx-auto">
                        </div>
                    @endif
                    @if($pledgeForm->signature_three)
                        <div class="col-md-6 mb-3 text-center">
                            <div class="q-label mb-2">{{ __('messages.signature') }} 3</div>
                            <img src="{{ asset('assets/admin/uploads/' . $pledgeForm->signature_three) }}"
                                 class="sig-img d-block mx-auto">
                        </div>
                    @endif
                    @if($pledgeForm->signature_four)
                        <div class="col-md-6 mb-3 text-center">
                            <div class="q-label mb-2">{{ __('messages.signature') }} 4</div>
                            <img src="{{ asset('assets/admin/uploads/' . $pledgeForm->signature_four) }}"
                                 class="sig-img d-block mx-auto">
                        </div>
                    @endif
                    @if(!$pledgeForm->signature_one && !$pledgeForm->signature_two && !$pledgeForm->signature_three && !$pledgeForm->signature_four)
                        <div class="col-12 text-center text-muted py-3">
                            <i class="fas fa-pen-nib fa-2x mb-2 d-block"></i>
                            {{ __('messages.no_signatures') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    {{-- ═══════════════════════════════════════════════
         AUTHORIZATION FORM
    ════════════════════════════════════════════════ --}}
    @else

        {{-- Section 1: Basic info --}}
        <div class="card mb-3 border {{ $borderClass }}">
            <div class="card-header {{ $headerClass }} text-white py-2">
                <i class="fas fa-info-circle mr-1"></i>
                {{ __('messages.basic_information') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.auth_date') }}</div>
                        <div class="q-answer {{ !$pledgeForm->date_of_pledge ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->date_of_pledge ? $pledgeForm->date_of_pledge->format('Y-m-d') : '-' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.auth_place') }}</div>
                        <div class="q-answer {{ !$pledgeForm->place ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->place ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Patient info --}}
        <div class="card mb-3 border {{ $borderClass }}">
            <div class="card-header {{ $headerClass }} text-white py-2">
                <i class="fas fa-procedures mr-1"></i>
                {{ __('messages.patient_information') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.auth_patient_full_name') }}</div>
                        <div class="q-answer {{ !$pledgeForm->name_of_patient ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->name_of_patient ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.auth_patient_national_number') }}</div>
                        <div class="q-answer {{ !$pledgeForm->identity_number_of_patient ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->identity_number_of_patient ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.auth_date_of_birth') }}</div>
                        <div class="q-answer {{ !$pledgeForm->date_of_birth ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->date_of_birth ? $pledgeForm->date_of_birth->format('Y-m-d') : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Guardian / Relative info --}}
        <div class="card mb-3 border {{ $borderClass }}">
            <div class="card-header {{ $headerClass }} text-white py-2">
                <i class="fas fa-users mr-1"></i>
                {{ __('messages.auth_guardian_relative_info') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.auth_first_degree_relative_guardian_name') }}</div>
                        <div class="q-answer {{ !$pledgeForm->parent_of_patient ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->parent_of_patient ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.auth_guardian_national_number') }}</div>
                        <div class="q-answer {{ !$pledgeForm->identity_number_for_parent_of_patient ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->identity_number_for_parent_of_patient ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.auth_guardian_phone') }}</div>
                        <div class="q-answer {{ !$pledgeForm->phone_for_parent_of_patient ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->phone_for_parent_of_patient ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.auth_relationship') }}</div>
                        <div class="q-answer {{ !$pledgeForm->kinship ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->kinship ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- Guardian signature --}}
                @if($pledgeForm->signature_one)
                    <hr class="section-divider">
                    <div class="text-center">
                        <div class="q-label mb-2">{{ __('messages.auth_guardian_signature') }}</div>
                        <img src="{{ asset('assets/admin/uploads/' . $pledgeForm->signature_one) }}"
                             class="sig-img d-block mx-auto">
                    </div>
                @endif
            </div>
        </div>

        {{-- Section 4: Static authorization text --}}
        <div class="card mb-3">
            <div class="card-header bg-light py-2">
                <i class="fas fa-scroll mr-1 {{ $colorClass }}"></i>
                <strong>{{ __('messages.auth_authorization_statement_title') }}</strong>
            </div>
            <div class="card-body">
                <div class="static-text-box mb-3">{{ __('messages.auth_signatories_below') }}</div>
                <div class="static-text-box mb-3">{{ __('messages.auth_authorization_statement') }}</div>
                <div class="static-text-box mb-3">{{ __('messages.auth_nursing_procedures_statement') }}</div>
                <div class="static-text-box mb-3">{{ __('messages.auth_nursing_procedures_list') }}</div>
                <div class="static-text-box">{{ __('messages.auth_responsibility_statement') }}</div>
            </div>
        </div>

        {{-- Section 5: Authorized person --}}
        <div class="card mb-3 border {{ $borderClass }}">
            <div class="card-header {{ $headerClass }} text-white py-2">
                <i class="fas fa-user-md mr-1"></i>
                {{ __('messages.auth_authorized_person_info') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.auth_authorized_person_full_name') }}</div>
                        <div class="q-answer {{ !$pledgeForm->full_name_of_commissioner ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->full_name_of_commissioner ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.auth_medical_entity_nurse') }}</div>
                        <div class="q-answer {{ !$pledgeForm->name_of_nurse ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->name_of_nurse ?? '-' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="q-label">{{ __('messages.auth_license_number') }}</div>
                        <div class="q-answer {{ !$pledgeForm->professional_license_number ? 'empty-answer' : '' }}">
                            {{ $pledgeForm->professional_license_number ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- Signatures --}}
                @if($pledgeForm->signature_two || $pledgeForm->signature_three || $pledgeForm->signature_four)
                    <hr class="section-divider">
                    <div class="row">
                        @if($pledgeForm->signature_three)
                            <div class="col-md-6 mb-3 text-center">
                                <div class="q-label mb-2">{{ __('messages.auth_patient_signature') }}</div>
                                <img src="{{ asset('assets/admin/uploads/' . $pledgeForm->signature_three) }}"
                                     class="sig-img d-block mx-auto">
                            </div>
                        @endif
                        @if($pledgeForm->signature_two)
                            <div class="col-md-6 mb-3 text-center">
                                <div class="q-label mb-2">{{ __('messages.auth_commissioner_signature') }}</div>
                                <img src="{{ asset('assets/admin/uploads/' . $pledgeForm->signature_two) }}"
                                     class="sig-img d-block mx-auto">
                            </div>
                        @endif
                        @if($pledgeForm->signature_four)
                            <div class="col-md-6 mb-3 text-center">
                                <div class="q-label mb-2">{{ __('messages.signature') }} 4</div>
                                <img src="{{ asset('assets/admin/uploads/' . $pledgeForm->signature_four) }}"
                                     class="sig-img d-block mx-auto">
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    @endif

</div>
</div>
@endsection
