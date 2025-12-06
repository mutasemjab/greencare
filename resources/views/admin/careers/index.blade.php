@extends('layouts.admin')

@section('title', __('messages.careers'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.careers') }}</h3>
                    <a href="{{ route('careers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.add_new') }}
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.id') }}</th>
                                    <th>{{ __('messages.title') }}</th>
                                    <th>{{ __('messages.description') }}</th>
                                    <th>{{ __('messages.applications_count') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($careers as $career)
                                    <tr>
                                        <td>{{ $career->id }}</td>
                                        <td>{{ $career->title }}</td>
                                        <td>{{ Str::limit($career->description, 50) }}</td>
                                        <td>
                                            <a href="{{ route('careers.applications', $career) }}" class="badge bg-info">
                                                {{ $career->applications_count }} {{ __('messages.applications') }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($career->is_active)
                                                <span class="badge bg-success">{{ __('messages.active') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('messages.inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('careers.edit', $career) }}" 
                                                   class="btn btn-sm btn-warning">
                                                    {{ __('messages.edit') }}
                                                </a>
                                                <form action="{{ route('careers.destroy', $career) }}" 
                                                      method="POST" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        {{ __('messages.delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            {{ __('messages.no_data_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $careers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection