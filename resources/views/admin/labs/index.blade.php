@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.Labs') }}</h3>
                    @can('lab-add')
                        <a href="{{ route('labs.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.Add_Lab') }}
                        </a>
                    @endcan
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.Photo') }}</th>
                                    <th>{{ __('messages.Name') }}</th>
                                    <th>{{ __('messages.Phone') }}</th>
                                    <th>{{ __('messages.Email') }}</th>
                                    <th>{{ __('messages.License_Number') }}</th>
                                    <th>{{ __('messages.Status') }}</th>
                                    <th>{{ __('messages.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($labs as $lab)
                                    <tr>
                                        <td>{{ $loop->iteration + ($labs->currentPage() - 1) * $labs->perPage() }}</td>
                                        <td>
                                            @if($lab->photo)
                                                <img src="{{ asset('assets/admin/uploads/'. $lab->photo) }}" 
                                                     alt="{{ $lab->name }}" 
                                                     class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-hospital text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $lab->name }}</strong>
                                        </td>
                                        <td>{{ $lab->phone }}</td>
                                        <td>{{ $lab->email ?? '-' }}</td>
                                        <td>{{ $lab->license_number ?? '-' }}</td>
                                        <td>
                                            @if($lab->activate == 1)
                                                <span class="badge bg-success">{{ __('messages.Active') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('messages.Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('labs.show', $lab) }}" 
                                                   class="btn btn-sm btn-info" title="{{ __('messages.View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('lab-edit')
                                                    <a href="{{ route('labs.edit', $lab) }}" 
                                                       class="btn btn-sm btn-warning" title="{{ __('messages.Edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('lab-edit')
                                                    <form action="{{ route('labs.toggle-activation', $lab) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-sm {{ $lab->activate == 1 ? 'btn-secondary' : 'btn-success' }}" 
                                                                title="{{ $lab->activate == 1 ? __('messages.Deactivate') : __('messages.Activate') }}">
                                                            <i class="fas {{ $lab->activate == 1 ? 'fa-ban' : 'fa-check' }}"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('lab-delete')
                                                    <form action="{{ route('labs.destroy', $lab) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('{{ __('messages.Are_You_Sure_Delete') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="{{ __('messages.Delete') }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('messages.No_Labs_Found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $labs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection