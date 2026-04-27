@extends('layouts.app')

@section('title', 'الإشعارات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-bell me-2"></i> الإشعارات
        @if($unreadCount > 0)
            <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
        @endif
    </h4>
    <div class="d-flex gap-2">
        @if($unreadCount > 0)
            <button class="btn btn-sm btn-outline-primary" onclick="markAllRead()">
                <i class="bi bi-check-all"></i> تحديد الكل كمقروء
            </button>
        @endif
        <form method="POST" action="{{ route('lab.notifications.deleteAllRead') }}"
              onsubmit="return confirm('حذف جميع الإشعارات المقروءة؟')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash"></i> حذف المقروءة
            </button>
        </form>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-3 flex-wrap align-items-center">
            <select name="type" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="all" {{ request('type','all') === 'all' ? 'selected' : '' }}>كل الأنواع</option>
                <option value="medical_test" {{ request('type') === 'medical_test' ? 'selected' : '' }}>تحاليل</option>
                <option value="home_xray"    {{ request('type') === 'home_xray'    ? 'selected' : '' }}>أشعة منزلية</option>
            </select>
            <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value=""       {{ !request('status') ? 'selected' : '' }}>كل الحالات</option>
                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>غير مقروء</option>
                <option value="read"   {{ request('status') === 'read'   ? 'selected' : '' }}>مقروء</option>
            </select>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="list-group list-group-flush">
        @forelse($notifications as $notification)
            <div class="list-group-item {{ !$notification->is_read ? 'bg-warning bg-opacity-10' : '' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex gap-3">
                        <div class="mt-1">
                            <i class="{{ $notification->icon }} text-{{ $notification->badge_color }} fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $notification->title }}
                                @if(!$notification->is_read)
                                    <span class="badge bg-danger ms-1" style="font-size:0.65rem;">جديد</span>
                                @endif
                            </div>
                            <div class="text-muted small">{{ $notification->message }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $notification->created_at_human }}</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 ms-3">
                        @if($notification->url)
                            <a href="{{ $notification->url }}"
                               onclick="markRead({{ $notification->id }}, event)"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        @endif
                        @if(!$notification->is_read)
                            <button class="btn btn-sm btn-outline-success" onclick="markRead({{ $notification->id }})">
                                <i class="bi bi-check"></i>
                            </button>
                        @endif
                        <form method="POST" action="{{ route('lab.notifications.destroy', $notification->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('حذف هذا الإشعار؟')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="list-group-item text-center text-muted py-5">
                <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                لا توجد إشعارات
            </div>
        @endforelse
    </div>
</div>

<div class="mt-3">{{ $notifications->withQueryString()->links() }}</div>

@endsection

@push('scripts')
<script>
function markRead(id, event) {
    if (event) event.preventDefault();
    const url = event?.currentTarget.getAttribute('href');
    fetch(`/lab/notifications/${id}/mark-read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(() => {
        if (url) window.location.href = url;
        else location.reload();
    });
}

function markAllRead() {
    fetch('{{ route("lab.notifications.markAllAsRead") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(() => location.reload());
}
</script>
@endpush
