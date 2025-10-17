@extends('layouts.admin')

@section('title', __('messages.pledge_forms'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.pledge_forms') }}</h3>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('pledge-forms.index') }}" class="d-flex">
                                <input type="text" 
                                       name="search" 
                                       class="form-control me-2" 
                                       placeholder="{{ __('messages.search') }}" 
                                       value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary">{{ __('messages.search') }}</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('pledge-forms.index') }}" class="d-flex">
                                <select name="type" class="form-control me-2" onchange="this.form.submit()">
                                    <option value="">{{ __('messages.all_types') }}</option>
                                    <option value="pledge_form" {{ request('type') == 'pledge_form' ? 'selected' : '' }}>
                                        {{ __('messages.pledge_form') }}
                                    </option>
                                    <option value="authorization_form" {{ request('type') == 'authorization_form' ? 'selected' : '' }}>
                                        {{ __('messages.authorization_form') }}
                                    </option>
                                </select>
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            </form>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.id') }}</th>
                                    <th>{{ __('messages.nurse_name') }}</th>
                                    <th>{{ __('messages.patient_name') }}</th>
                                    <th>{{ __('messages.patient_identity') }}</th>
                                    <th>{{ __('messages.type') }}</th>
                                    <th>{{ __('messages.room') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pledgeForms as $form)
                                    <tr>
                                        <td>{{ $form->id }}</td>
                                        <td>{{ $form->name_of_nurse }}</td>
                                        <td>{{ $form->name_of_patient }}</td>
                                        <td>{{ $form->identity_number_of_patient }}</td>
                                        <td>
                                            <span class="badge bg-{{ $form->type == 'pledge_form' ? 'primary' : 'success' }}">
                                                {{ __('messages.' . $form->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $form->room->name ?? __('messages.not_available') }}</td>
                                        <td>{{ $form->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('pledge-forms.show', $form) }}" 
                                               class="btn btn-sm btn-info">
                                                {{ __('messages.view') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            {{ __('messages.no_data_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $pledgeForms->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection