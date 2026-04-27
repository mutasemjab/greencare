<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة تحكم المختبر')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }

        .sidebar {
            min-height: 100vh;
            background: #2c3e50;
        }

        .sidebar a {
            color: #ecf0f1;
            padding: 15px 20px;
            display: block;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #34495e;
        }

        .sidebar a.active {
            background: #3498db;
        }

        .content {
            padding: 20px;
        }

        /* Notification styles */
        @keyframes pulse {
            0%   { box-shadow: 0 0 0 0 rgba(220,53,69,.7); transform: scale(1); }
            50%  { box-shadow: 0 0 0 5px rgba(220,53,69,0); transform: scale(1.1); }
            100% { box-shadow: 0 0 0 0 rgba(220,53,69,0); transform: scale(1); }
        }
        .pulse-animation { animation: pulse 1s ease-in-out 3; }
        .notif-dropdown  { min-width: 320px; max-width: 360px; max-height: 400px; overflow-y: auto; }
        .notif-item      { white-space: normal; padding: 10px 15px; border-right: 3px solid transparent; }
        .notif-item.unread { background: #fff3cd; border-right-color: #ffc107; }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block sidebar p-0">
                <div class="position-sticky">
                    <div class="text-center py-4 text-white">
                        <h5>{{ auth('lab')->user()->name }}</h5>
                        <small>{{ auth('lab')->user()->license_number }}</small>
                    </div>

                    {{-- resources/views/lab/layouts/app.blade.php - تحديث جزء الـ Sidebar --}}
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('lab.dashboard') ? 'active' : '' }}"
                                href="{{ route('lab.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> الرئيسية
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('lab.appointments.*') ? 'active' : '' }}"
                                href="{{ route('lab.appointments.index') }}">
                                <i class="bi bi-calendar-check"></i> المواعيد
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('lab.appointments.index') && request('status') == 'pending' ? 'active' : '' }}"
                                href="{{ route('lab.appointments.index', ['status' => 'pending']) }}">
                                <i class="bi bi-clock-history"></i> المواعيد المعلقة
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('lab.appointments.index') && request('status') == 'processing' ? 'active' : '' }}"
                                href="{{ route('lab.appointments.index', ['status' => 'processing']) }}">
                                <i class="bi bi-gear"></i> قيد المعالجة
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('lab.appointments.index') && request('status') == 'finished' ? 'active' : '' }}"
                                href="{{ route('lab.appointments.index', ['status' => 'finished']) }}">
                                <i class="bi bi-check-circle"></i> المنتهية
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('lab.notifications.*') ? 'active' : '' }}"
                                href="{{ route('lab.notifications.index') }}">
                                <i class="bi bi-bell"></i> الإشعارات
                                @php $sidebarUnread = auth('lab')->check() ? \App\Models\LabNotification::forLab(auth('lab')->id())->unread()->count() : 0; @endphp
                                @if($sidebarUnread > 0)
                                    <span class="badge bg-danger ms-1">{{ $sidebarUnread }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                            <ul class="navbar-nav align-items-center gap-2">

                                <!-- Notification Bell -->
                                <li class="nav-item dropdown">
                                    @php $labUnread = auth('lab')->check() ? \App\Models\LabNotification::forLab(auth('lab')->id())->unread()->count() : 0; @endphp
                                    <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown" id="labNotifDropdown">
                                        <i class="bi bi-bell fs-5"></i>
                                        <span id="labNotifBadge"
                                              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                              style="font-size:.6rem; display:{{ $labUnread > 0 ? 'inline-block' : 'none' }}">
                                            <span id="labNotifCount">{{ $labUnread }}</span>
                                        </span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0">
                                        <div class="px-3 py-2 bg-light border-bottom fw-bold">
                                            <span id="labNotifHeaderCount">{{ $labUnread }}</span> إشعار جديد
                                        </div>
                                        <div id="labNotifList">
                                            @forelse(\App\Models\LabNotification::forLab(auth('lab')->id())->unread()->orderByDesc('created_at')->take(5)->get() as $n)
                                                <a href="{{ $n->url ?? '#' }}"
                                                   class="dropdown-item notif-item unread"
                                                   onclick="labMarkRead({{ $n->id }}, event)">
                                                    <i class="{{ $n->icon }} text-{{ $n->badge_color }} me-2"></i>
                                                    <span class="small">{{ Str::limit($n->message, 55) }}</span>
                                                    <div class="text-muted" style="font-size:.72rem;">{{ $n->created_at->diffForHumans() }}</div>
                                                </a>
                                                <div class="dropdown-divider my-0"></div>
                                            @empty
                                                <div class="dropdown-item text-center text-muted" id="labNoNotif">
                                                    <i class="bi bi-bell-slash me-1"></i> لا توجد إشعارات
                                                </div>
                                                <div class="dropdown-divider my-0"></div>
                                            @endforelse
                                        </div>
                                        <a href="{{ route('lab.notifications.index') }}" class="dropdown-item text-center fw-semibold py-2 bg-light border-top">
                                            عرض كل الإشعارات
                                        </a>
                                    </div>
                                </li>

                                <!-- User Menu -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown">
                                        {{ auth('lab')->user()->name }}
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('lab.notifications.index') }}">
                                                <i class="bi bi-bell me-2"></i> الإشعارات
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('lab.logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-box-arrow-right"></i> تسجيل خروج
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Content -->
                <div class="content">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @auth('lab')
    <script>
        let labLastCount = parseInt(document.getElementById('labNotifCount')?.textContent) || 0;

        function labMarkRead(id, event) {
            if (event) event.preventDefault();
            const href = event?.currentTarget.getAttribute('href');
            fetch(`/lab/notifications/${id}/mark-read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => {
                labUpdateCount();
                if (href && href !== '#') window.location.href = href;
            });
        }

        function labUpdateCount() {
            fetch('{{ route("lab.notifications.unreadCount") }}')
                .then(r => r.json())
                .then(data => {
                    const badge = document.getElementById('labNotifBadge');
                    const count = document.getElementById('labNotifCount');
                    const header = document.getElementById('labNotifHeaderCount');
                    if (badge && count) {
                        count.textContent  = data.count;
                        if (header) header.textContent = data.count;
                        badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                    }
                });
        }

        function labLoadNotifications() {
            fetch('{{ route("lab.notifications.latest") }}')
                .then(r => r.json())
                .then(data => {
                    const list = document.getElementById('labNotifList');
                    if (!list) return;
                    if (data.notifications && data.notifications.length > 0) {
                        list.innerHTML = data.notifications.map(n => `
                            <a href="${n.url || '#'}" class="dropdown-item notif-item unread"
                               onclick="labMarkRead(${n.id}, event)">
                                <i class="${n.icon} text-${n.badge_color} me-2"></i>
                                <span class="small">${n.message.substring(0, 55)}${n.message.length > 55 ? '...' : ''}</span>
                                <div class="text-muted" style="font-size:.72rem;">${n.created_at_human}</div>
                            </a>
                            <div class="dropdown-divider my-0"></div>
                        `).join('');
                    } else {
                        list.innerHTML = `<div class="dropdown-item text-center text-muted"><i class="bi bi-bell-slash me-1"></i> لا توجد إشعارات</div><div class="dropdown-divider my-0"></div>`;
                    }
                });
        }

        function labCheckNewNotifications() {
            fetch('{{ route("lab.notifications.unreadCount") }}')
                .then(r => r.json())
                .then(data => {
                    const badge  = document.getElementById('labNotifBadge');
                    const count  = document.getElementById('labNotifCount');
                    const header = document.getElementById('labNotifHeaderCount');

                    if (data.count > labLastCount) {
                        if (badge && count) {
                            count.textContent  = data.count;
                            if (header) header.textContent = data.count;
                            badge.textContent  = data.count;
                            badge.style.display = 'inline-block';
                            badge.classList.add('pulse-animation');
                            setTimeout(() => badge.classList.remove('pulse-animation'), 2000);
                        }
                        labLoadNotifications();

                        // Browser notification
                        fetch('{{ route("lab.notifications.latest") }}')
                            .then(r => r.json())
                            .then(d => {
                                if (d.notifications?.length && 'Notification' in window && Notification.permission === 'granted') {
                                    const n = d.notifications[0];
                                    const notif = new Notification(n.title, {
                                        body: n.message,
                                        icon: '{{ asset("images/logo.png") }}'
                                    });
                                    notif.onclick = () => { window.focus(); if (n.url) window.location.href = n.url; };
                                    setTimeout(() => notif.close(), 5000);
                                }
                            });

                        labLastCount = data.count;
                    } else if (data.count !== labLastCount) {
                        if (badge && count) {
                            count.textContent = data.count;
                            if (header) header.textContent = data.count;
                            badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                        }
                        labLastCount = data.count;
                    }
                })
                .catch(() => {});
        }

        document.addEventListener('DOMContentLoaded', function () {
            labLastCount = parseInt(document.getElementById('labNotifCount')?.textContent) || 0;
            setInterval(labCheckNewNotifications, 10000);
            if ('Notification' in window && Notification.permission === 'default') {
                setTimeout(() => Notification.requestPermission(), 3000);
            }
        });
    </script>
    @endauth

    @stack('scripts')
</body>

</html>
