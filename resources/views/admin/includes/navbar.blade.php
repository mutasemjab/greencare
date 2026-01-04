<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">{{__('messages.Home')}}</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('admin.logout') }}" class="nav-link">{{__('messages.Logout')}}</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Language Switcher -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-globe"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right p-0">
                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <a class="dropdown-item" hreflang="{{ $localeCode }}" 
                       href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                        <i class="fas fa-language mr-2"></i> {{ $properties['native'] }}
                    </a>
                @endforeach
            </div>
        </li>

        <!-- Notifications Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" id="notificationDropdown">
                <i class="far fa-bell"></i>
                @php
                    $unreadCount = \App\Models\AdminNotification::unread()->count();
                @endphp
                <span class="badge badge-danger navbar-badge" id="notificationBadge" style="display: {{ $unreadCount > 0 ? 'inline-block' : 'none' }}">
                    {{ $unreadCount }}
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">
                    <span id="notificationCount">{{ $unreadCount }}</span> 
                    {{ __('messages.new_notifications') }}
                </span>
                <div class="dropdown-divider"></div>
                
                <div id="notificationList">
                    @php
                        $latestNotifications = \App\Models\AdminNotification::unread()
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @forelse($latestNotifications as $notification)
                        <a href="{{ $notification->url ?? '#' }}" 
                           class="dropdown-item notification-item {{ !$notification->is_read ? 'unread' : '' }}" 
                           onclick="markAsRead('{{ $notification->id }}', event)">
                            <i class="{{ $notification->icon }} mr-2 text-{{ $notification->badge_color }}"></i> 
                            <span class="text-sm">{{ Str::limit($notification->message, 50) }}</span>
                            <span class="float-right text-muted text-xs">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </a>
                        <div class="dropdown-divider"></div>
                    @empty
                        <div class="dropdown-item text-center text-muted" id="noNotifications">
                            <i class="fas fa-info-circle mr-2"></i> 
                            {{ __('messages.no_notifications') }}
                        </div>
                        <div class="dropdown-divider"></div>
                    @endforelse
                </div>
                
                <a href="{{ route('admin.notifications.index') }}" class="dropdown-item dropdown-footer">
                    {{ __('messages.see_all_notifications') }}
                </a>
            </div>
        </li>

        <!-- User Profile -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle"></i>
                <span class="d-none d-md-inline ml-1">{{ auth()->user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> {{ __('messages.profile') }}
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('admin.logout') }}" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt mr-2"></i> {{ __('messages.Logout') }}
                </a>
            </div>
        </li>
    </ul>
</nav>