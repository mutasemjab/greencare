{{-- resources/views/lab/appointments/partials/status-badge.blade.php --}}
@switch($status)
    @case('pending')
        <span class="badge bg-warning text-dark">قيد الانتظار</span>
        @break
    @case('confirmed')
        <span class="badge bg-primary">مؤكد</span>
        @break
    @case('processing')
        <span class="badge bg-info">قيد المعالجة</span>
        @break
    @case('finished')
        <span class="badge bg-success">منتهي</span>
        @break
    @case('cancelled')
        <span class="badge bg-danger">ملغي</span>
        @break
    @default
        <span class="badge bg-secondary">{{ $status }}</span>
@endswitch