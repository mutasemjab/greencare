@extends('layouts.admin')

@section('title', __('messages.notifications'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ __('messages.notifications') }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.notifications') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">{{ __('messages.total_notifications') }}</span>
                            <h4 class="mb-3">{{ number_format($notifications->total()) }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary">
                                <span class="avatar-title bg-primary rounded-circle">
                                    <i class="ri-notification-line font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">{{ __('messages.unread_notifications') }}</span>
                            <h4 class="mb-3">{{ number_format($unreadCount) }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-warning">
                                <span class="avatar-title bg-warning rounded-circle">
                                    <i class="ri-mail-unread-line font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">{{ __('messages.read_notifications') }}</span>
                            <h4 class="mb-3">{{ number_format($notifications->total() - $unreadCount) }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success">
                                <span class="avatar-title bg-success rounded-circle">
                                    <i class="ri-mail-check-line font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="card-title mb-0">{{ __('messages.filter_notifications') }}</h4>
                        </div>
                        <div class="col-md-6 text-right">
                            @if($unreadCount > 0)
                                <button type="button" class="btn btn-sm btn-primary" onclick="markAllAsRead()">
                                    <i class="ri-check-double-line"></i> {{ __('messages.mark_all_as_read') }}
                                </button>
                            @endif
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteAllRead()">
                                <i class="ri-delete-bin-line"></i> {{ __('messages.delete_all_read') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.notifications.index') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('messages.notification_type') }}</label>
                                <select name="type" class="form-control">
                                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>{{ __('messages.all_types') }}</option>
                                    <option value="order" {{ request('type') == 'order' ? 'selected' : '' }}>{{ __('messages.orders') }}</option>
                                    <option value="appointment" {{ request('type') == 'appointment' ? 'selected' : '' }}>{{ __('messages.appointments') }}</option>
                                    <option value="appointment_provider" {{ request('type') == 'appointment_provider' ? 'selected' : '' }}>{{ __('messages.appointment_providers') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('messages.status') }}</label>
                                <select name="status" class="form-control">
                                    <option value="" {{ request('status') == '' ? 'selected' : '' }}>{{ __('messages.all_statuses') }}</option>
                                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>{{ __('messages.unread') }}</option>
                                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>{{ __('messages.read') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ri-search-line"></i> {{ __('messages.filter') }}
                                </button>
                                <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                                    <i class="ri-refresh-line"></i> {{ __('messages.reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('messages.notifications_list') }}</h4>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                <div class="list-group-item {{ !$notification->is_read ? 'list-group-item-warning' : '' }}">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="{{ $notification->icon }} text-{{ $notification->badge_color }} me-2"></i>
                                                <h5 class="mb-0">{{ $notification->title }}</h5>
                                                <span class="badge bg-{{ $notification->badge_color }} ms-2">
                                                    {{ __('messages.' . $notification->type) }}
                                                </span>
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-warning ms-2">{{ __('messages.new') }}</span>
                                                @endif
                                            </div>
                                            <p class="mb-2">{{ $notification->message }}</p>
                                            <small class="text-muted">
                                                <i class="ri-time-line"></i> {{ $notification->created_at->diffForHumans() }}
                                                @if($notification->is_read)
                                                    | <i class="ri-check-line"></i> {{ __('messages.read_at') }}: {{ $notification->read_at->diffForHumans() }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="btn-group">
                                            @if($notification->url)
                                                <a href="{{ $notification->url }}" class="btn btn-sm btn-info" 
                                                   onclick="markNotificationAsRead({{ $notification->id }}, event)">
                                                    <i class="ri-eye-line"></i> {{ __('messages.view') }}
                                                </a>
                                            @endif
                                            @if(!$notification->is_read)
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="markNotificationAsRead({{ $notification->id }})">
                                                    <i class="ri-check-line"></i> {{ __('messages.mark_as_read') }}
                                                </button>
                                            @endif
                                            <form action="{{ route('admin.notifications.destroy', $notification->id) }}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('{{ __('messages.confirm_delete_notification') }}')">
                                                    <i class="ri-delete-bin-line"></i> {{ __('messages.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="row mt-4">
                            <div class="col-12">
                                {{ $notifications->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ri-notification-off-line font-size-48 text-muted"></i>
                            <h5 class="mt-3">{{ __('messages.no_notifications_found') }}</h5>
                            <p class="text-muted">{{ __('messages.no_notifications_message') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Mark notification as read
function markNotificationAsRead(notificationId, event = null) {
    if (event) {
        event.preventDefault();
    }
    
    const url = event ? event.currentTarget.getAttribute('href') : null;
    
    fetch(`/admin/notifications/${notificationId}/mark-as-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (url) {
                window.location.href = url;
            } else {
                location.reload();
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

// Mark all as read
function markAllAsRead() {
    if (!confirm('{{ __("messages.confirm_mark_all_as_read") }}')) {
        return;
    }
    
    fetch('/admin/notifications/mark-all-as-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

// Delete all read notifications
function deleteAllRead() {
    if (!confirm('{{ __("messages.confirm_delete_all_read") }}')) {
        return;
    }
    
    fetch('/admin/notifications/read/all', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<style>
.list-group-item-warning {
    background-color: #fff3cd;
    border-left: 4px solid #ffc107;
}

.list-group-item {
    transition: all 0.3s ease;
}

.list-group-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}
</style>
@endsection