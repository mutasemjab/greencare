<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                            <ul class="navbar-nav">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown">
                                        {{ auth('lab')->user()->name }}
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
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
    @stack('scripts')
</body>

</html>
