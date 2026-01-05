@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('messages.Lab_Details') }}</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Photo -->
                        @if($lab->photo)
                            <div class="col-md-12 mb-4 text-center">
                                <img src="{{ asset('assets/admin/uploads/labs/' . $lab->photo) }}" 
                                     alt="{{ $lab->name }}" 
                                     class="img-thumbnail" 
                                     style="max-width: 300px;">
                            </div>
                        @endif

                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%;">{{ __('messages.Name') }}</th>
                                    <td>{{ $lab->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Phone') }}</th>
                                    <td>{{ $lab->phone }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Email') }}</th>
                                    <td>{{ $lab->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.License_Number') }}</th>
                                    <td>{{ $lab->license_number ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%;">{{ __('messages.Status') }}</th>
                                    <td>
                                        @if($lab->activate == 1)
                                            <span class="badge bg-success">{{ __('messages.Active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('messages.Inactive') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Address') }}</th>
                                    <td>{{ $lab->address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Created_At') }}</th>
                                    <td>{{ $lab->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('messages.Updated_At') }}</th>
                                    <td>{{ $lab->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        @if($lab->description)
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">{{ __('messages.Description') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">{{ $lab->description }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-footer">
                    @can('lab-edit')
                        <a href="{{ route('labs.edit', $lab) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> {{ __('messages.Edit') }}
                        </a>
                    @endcan
                    <a href="{{ route('labs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('messages.Back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection