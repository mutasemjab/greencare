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

<!-- Add this script at the bottom of your layout file (before closing </body> tag) -->
<script>
// Mark notification as read
function markAsRead(notificationId, event) {
    event.preventDefault();
    const link = event.currentTarget;
    const url = link.getAttribute('href');
    
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
            // Update notification count
            updateNotificationCount();
            // Redirect to the notification URL
            if (url !== '#') {
                window.location.href = url;
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

// Update notification count
function updateNotificationCount() {
    fetch('/admin/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificationBadge');
            const count = document.getElementById('notificationCount');
            
            if (badge && count) {
                count.textContent = data.count;
                badge.textContent = data.count;
                
                if (data.count > 0) {
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error:', error));
}

// Load notifications via AJAX
function loadNotifications() {
    fetch('/admin/notifications/latest')
        .then(response => response.json())
        .then(data => {
            const notificationList = document.getElementById('notificationList');
            
            if (data.notifications && data.notifications.length > 0) {
                let html = '';
                data.notifications.forEach(notification => {
                    const message = notification.message.length > 50 
                        ? notification.message.substring(0, 50) + '...' 
                        : notification.message;
                    
                    html += `
                        <a href="${notification.url || '#'}" 
                           class="dropdown-item notification-item unread" 
                           onclick="markAsRead('${notification.id}', event)">
                            <i class="${notification.icon} mr-2 text-${notification.badge_color}"></i> 
                            <span class="text-sm">${message}</span>
                            <span class="float-right text-muted text-xs">
                                ${notification.created_at_human}
                            </span>
                        </a>
                        <div class="dropdown-divider"></div>
                    `;
                });
                notificationList.innerHTML = html;
            } else {
                notificationList.innerHTML = `
                    <div class="dropdown-item text-center text-muted" id="noNotifications">
                        <i class="fas fa-info-circle mr-2"></i> 
                        {{ __('messages.no_notifications') }}
                    </div>
                    <div class="dropdown-divider"></div>
                `;
            }
        })
        .catch(error => console.error('Error loading notifications:', error));
}

// Auto-refresh notification count every 30 seconds
setInterval(function() {
    fetch('/admin/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificationBadge');
            const count = document.getElementById('notificationCount');
            const currentCount = parseInt(count.textContent) || 0;
            
            if (data.count > currentCount) {
                // New notification arrived!
                count.textContent = data.count;
                badge.textContent = data.count;
                badge.style.display = 'inline-block';
                
                // Add pulse animation
                badge.classList.add('pulse-animation');
                setTimeout(() => badge.classList.remove('pulse-animation'), 2000);
                
                // Optional: Play notification sound
                playNotificationSound();
                
                // Reload notification list
                loadNotifications();
                
                // Optional: Show toast notification
                showToast('{{ __("messages.new_notification") }}', '{{ __("messages.you_have_new_notifications") }}');
            } else if (data.count !== currentCount) {
                count.textContent = data.count;
                badge.textContent = data.count;
                
                if (data.count === 0) {
                    badge.style.display = 'none';
                } else {
                    badge.style.display = 'inline-block';
                }
            }
        })
        .catch(error => console.log('Error checking notifications:', error));
}, 30000); // Check every 30 seconds

// Play notification sound (optional)
function playNotificationSound() {
    // Uncomment to enable sound
    // const audio = new Audio('/sounds/notification.mp3');
    // audio.play().catch(e => console.log('Could not play sound'));
}

// Show toast notification (optional - requires a toast library or custom implementation)
function showToast(title, message) {
    // Example using basic browser notification (if you want)
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification(title, {
            body: message,
            icon: '{{ asset("images/logo.png") }}',
            badge: '{{ asset("images/badge.png") }}'
        });
    }
    
    // Or you can use Toastr, SweetAlert, or Bootstrap Toast here
}

// Request notification permission on load (optional)
document.addEventListener('DOMContentLoaded', function() {
    if ('Notification' in window && Notification.permission === 'default') {
        // Uncomment to request permission automatically
        // Notification.requestPermission();
    }
});
</script>

<style>
/* Notification badge animation */
@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        transform: scale(1);
    }
    50% {
        box-shadow: 0 0 0 5px rgba(220, 53, 69, 0);
        transform: scale(1.1);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        transform: scale(1);
    }
}

.pulse-animation {
    animation: pulse 1s ease-in-out 3;
}

/* Dropdown notification styling */
.dropdown-menu-lg {
    max-width: 350px;
    min-width: 320px;
    max-height: 400px;
    overflow-y: auto;
}

.notification-item {
    white-space: normal;
    padding: 10px 15px;
    border-left: 3px solid transparent;
    transition: all 0.3s ease;
}

.notification-item.unread {
    background-color: #fff3cd;
    border-left-color: #ffc107;
}

.notification-item:hover {
    background-color: #f4f6f9;
    transform: translateX(3px);
}

.dropdown-item {
    white-space: normal;
}

.dropdown-header {
    padding: 10px 15px;
    font-weight: bold;
    background-color: #f8f9fa;
}

.dropdown-footer {
    text-align: center;
    padding: 10px;
    font-weight: 600;
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.dropdown-footer:hover {
    background-color: #e9ecef;
}

/* Badge styling */
.navbar-badge {
    font-size: 0.6rem;
    font-weight: 700;
    padding: 0.25em 0.4em;
    position: absolute;
    right: 5px;
    top: 5px;
}

/* Scrollbar styling for notification dropdown */
.dropdown-menu-lg::-webkit-scrollbar {
    width: 6px;
}

.dropdown-menu-lg::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.dropdown-menu-lg::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.dropdown-menu-lg::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>