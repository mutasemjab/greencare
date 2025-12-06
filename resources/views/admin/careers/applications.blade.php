@extends('layouts.admin')

@section('title', __('messages.career_applications'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        {{ __('messages.applications_for') }}: {{ $career->title }}
                    </h3>
                    <a href="{{ route('careers.index') }}" class="btn btn-secondary">
                        {{ __('messages.back_to_list') }}
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.id') }}</th>
                                    <th>{{ __('messages.applicant') }}</th>
                                    <th>{{ __('messages.email') }}</th>
                                    <th>{{ __('messages.cv') }}</th>
                                    <th>{{ __('messages.cover_letter') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.applied_at') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($applications as $application)
                                    <tr>
                                        <td>{{ $application->id }}</td>
                                        <td>{{ $application->user->name }}</td>
                                        <td>{{ $application->user->email }}</td>
                                        <td>
                                            <a href="{{ $application->cv_url }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-file-download"></i> {{ __('messages.download') }}
                                            </a>
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-secondary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#coverLetterModal{{ $application->id }}">
                                                <i class="fas fa-eye"></i> {{ __('messages.view') }}
                                            </button>

                                            <!-- Cover Letter Modal -->
                                            <div class="modal fade" id="coverLetterModal{{ $application->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('messages.cover_letter') }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            {{ $application->cover_letter }}
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                {{ __('messages.close') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <form action="{{ route('career-applications.update-status', $application) }}" 
                                                  method="POST">
                                                @csrf
                                                <select name="status" 
                                                        class="form-select form-select-sm" 
                                                        onchange="this.form.submit()">
                                                    <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>
                                                        {{ __('messages.status_pending') }}
                                                    </option>
                                                    <option value="reviewed" {{ $application->status == 'reviewed' ? 'selected' : '' }}>
                                                        {{ __('messages.status_reviewed') }}
                                                    </option>
                                                    <option value="accepted" {{ $application->status == 'accepted' ? 'selected' : '' }}>
                                                        {{ __('messages.status_accepted') }}
                                                    </option>
                                                    <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>
                                                        {{ __('messages.status_rejected') }}
                                                    </option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>{{ $application->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="mailto:{{ $application->user->email }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-envelope"></i> {{ __('messages.email') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            {{ __('messages.no_applications_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection