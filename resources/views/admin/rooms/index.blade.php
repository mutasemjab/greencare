@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ __('messages.rooms') }}</h3>
                    @can('room-add')
                        <a href="{{ route('rooms.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> {{ __('messages.add_room') }}
                        </a>
                    @endcan
                </div>

                <div class="card-body">
                  

                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('rooms.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="{{ __('messages.search_rooms') }}"
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="family_id" class="form-control">
                                    <option value="">{{ __('messages.all_families') }}</option>
                                    @foreach($families as $family)
                                        <option value="{{ $family->id }}" {{ request('family_id') == $family->id ? 'selected' : '' }}>
                                            {{ $family->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <div class="btn-group" role="group">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> {{ __('messages.search') }}
                                    </button>
                                    <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> {{ __('messages.clear') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.room_title') }}</th>
                                    <th>{{ __('messages.family') }}</th>
                                    <th>{{ __('messages.patients') }}</th>
                                    <th>{{ __('messages.doctors') }}</th>
                                    <th>{{ __('messages.nurses') }}</th>
                                    <th>{{ __('messages.reports') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rooms as $room)
                                    <tr>
                                        <td>{{ $loop->iteration + ($rooms->currentPage() - 1) * $rooms->perPage() }}</td>
                                        <td>
                                            <strong>{{ $room->title }}</strong>
                                            @if($room->description)
                                                <br><small class="text-muted">{{ Str::limit($room->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($room->family)
                                                <span class="badge badge-info">{{ $room->family->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">{{ $room->patients->count() }}</span>
                                            @if($room->patients->count() > 0)
                                                <div class="mt-1">
                                                    @foreach($room->patients->take(2) as $patient)
                                                        <small class="d-block text-muted">{{ $patient->name }}</small>
                                                    @endforeach
                                                    @if($room->patients->count() > 2)
                                                        <small class="text-muted">+{{ $room->patients->count() - 2 }} {{ __('messages.more') }}</small>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $room->doctors->count() }}</span>
                                            @if($room->doctors->count() > 0)
                                                <div class="mt-1">
                                                    @foreach($room->doctors->take(2) as $doctor)
                                                        <small class="d-block text-muted">{{ $doctor->name }}</small>
                                                    @endforeach
                                                    @if($room->doctors->count() > 2)
                                                        <small class="text-muted">+{{ $room->doctors->count() - 2 }} {{ __('messages.more') }}</small>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-success">{{ $room->nurses->count() }}</span>
                                            @if($room->nurses->count() > 0)
                                                <div class="mt-1">
                                                    @foreach($room->nurses->take(2) as $nurse)
                                                        <small class="d-block text-muted">{{ $nurse->name }}</small>
                                                    @endforeach
                                                    @if($room->nurses->count() > 2)
                                                        <small class="text-muted">+{{ $room->nurses->count() - 2 }} {{ __('messages.more') }}</small>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $room->reports->count() }}</span>
                                        </td>
                                        <td>{{ $room->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('room-table')
                                                    <a href="{{ route('rooms.show', $room) }}" 
                                                       class="btn btn-sm btn-info" 
                                                       title="{{ __('messages.view') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                
                                                {{-- ✅ Template History Button --}}
                                                @can('room-table')
                                                    <a href="{{ route('rooms.template-history', $room) }}" 
                                                       class="btn btn-sm btn-secondary" 
                                                       title="{{ __('messages.template_history') }}">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('room-edit')
                                                    <a href="{{ route('rooms.edit', $room) }}" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="{{ __('messages.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('room-delete')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            title="{{ __('messages.delete') }}"
                                                            data-toggle="modal" 
                                                            data-target="#deleteModal{{ $room->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>

                                            @can('room-delete')
                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal{{ $room->id }}" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">{{ __('messages.confirm_delete') }}</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                {{ __('messages.are_you_sure_delete_room') }} "<strong>{{ $room->title }}</strong>"?
                                                                <br><small class="text-muted">{{ __('messages.delete_room_warning') }}</small>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                    {{ __('messages.cancel') }}
                                                                </button>
                                                                <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">
                                                                        {{ __('messages.delete') }}
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">{{ __('messages.no_rooms_found') }}</p>
                                                @can('room-add')
                                                    <a href="{{ route('rooms.create') }}" class="btn btn-primary">
                                                        {{ __('messages.add_first_room') }}
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($rooms->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $rooms->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Tooltip initialization for better UX
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush