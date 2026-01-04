<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    
    <!-- Select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css') }}">
    
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="{{ asset('assets/admin/fonts/SansPro/SansPro.min.css') }}">
    
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/ffwdbcjhyfw4al7yr7y1e8shivh4g9nuipefj3gwz8y9s8h8/tinymce/5/tinymce.min.js"
        referrerpolicy="origin"></script>

    @if (App::getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap_rtl-v4.2.1/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap_rtl-v4.2.1/custom_rtl.css') }}">
    @endif
    
    <link rel="stylesheet" href="{{ asset('assets/admin/css/mycustomstyle.css') }}">
    
    <!-- Notification Styles -->
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
    
    @yield('css')
</head>

<body class="hold-transition sidebar-mini">
    <?php $user = auth()->user(); ?>
    <div class="wrapper">
        <!-- Navbar -->
        @include('admin.includes.navbar')
        
        <!-- Main Sidebar Container -->
        @include('admin.includes.sidebar')
        
        <!-- Content Wrapper. Contains page content -->
        @include('admin.includes.content')
        
        <!-- Footer -->
        @include('admin.includes.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- AdminLTE App -->
    <script src="{{ asset('assets/admin/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/general.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Notification Scripts -->
    <script>
        // Global variable to track last notification count
        let lastNotificationCount = parseInt(document.getElementById('notificationCount')?.textContent) || 0;

        // Mark notification as read
        function markAsRead(notificationId, event) {
            event.preventDefault();
            const link = event.currentTarget;
            const url = link.getAttribute('href');
            
            fetch('{{ route("admin.notifications.markAsRead", ":id") }}'.replace(':id', notificationId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationCount();
                    if (url !== '#') {
                        window.location.href = url;
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Update notification count
        function updateNotificationCount() {
            fetch('{{ route("admin.notifications.unreadCount") }}')
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
            fetch('{{ route("admin.notifications.latest") }}')
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

        // Show browser notification
        function showBrowserNotification(title, message, url, icon) {
            if ('Notification' in window && Notification.permission === 'granted') {
                const notification = new Notification(title, {
                    body: message,
                    icon: icon || '{{ asset("images/logo.png") }}',
                    badge: '{{ asset("images/badge.png") }}',
                    tag: 'admin-notification-' + Date.now(),
                    requireInteraction: false,
                    silent: false
                });

                // Play sound
                playNotificationSound();

                // Handle click
                notification.onclick = function(event) {
                    event.preventDefault();
                    window.focus();
                    if (url) {
                        window.location.href = url;
                    }
                    notification.close();
                };

                // Auto close after 5 seconds
                setTimeout(() => notification.close(), 5000);
            }
        }

        // Play notification sound
        function playNotificationSound() {
            try {
                const audio = new Audio('{{ asset("sounds/notification.mp3") }}');
                audio.volume = 0.5;
                audio.play().catch(e => console.log('Could not play sound:', e));
            } catch (e) {
                console.log('Sound not available');
            }
        }

        // Check for new notifications
        function checkForNewNotifications() {
            fetch('{{ route("admin.notifications.unreadCount") }}')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('notificationBadge');
                    const count = document.getElementById('notificationCount');
                    const currentCount = parseInt(count?.textContent) || 0;
                    
                    // New notification detected!
                    if (data.count > lastNotificationCount) {
                        console.log('New notification detected!', data.count, 'vs', lastNotificationCount);
                        
                        // Update badge
                        if (badge && count) {
                            count.textContent = data.count;
                            badge.textContent = data.count;
                            badge.style.display = 'inline-block';
                            
                            // Add pulse animation
                            badge.classList.add('pulse-animation');
                            setTimeout(() => badge.classList.remove('pulse-animation'), 2000);
                        }
                        
                        // Fetch latest notification details
                        fetch('{{ route("admin.notifications.latest") }}')
                            .then(response => response.json())
                            .then(notifData => {
                                if (notifData.notifications && notifData.notifications.length > 0) {
                                    // Get the newest notification
                                    const latestNotification = notifData.notifications[0];
                                    
                                    // Show browser notification
                                    showBrowserNotification(
                                        latestNotification.title,
                                        latestNotification.message,
                                        latestNotification.url,
                                        '{{ asset("images/logo.png") }}'
                                    );
                                    
                                    // Update notification list
                                    loadNotifications();
                                }
                            });
                        
                        // Update last count
                        lastNotificationCount = data.count;
                        
                    } else if (data.count !== currentCount) {
                        // Count changed but not increased (probably marked as read)
                        if (badge && count) {
                            count.textContent = data.count;
                            badge.textContent = data.count;
                            
                            if (data.count === 0) {
                                badge.style.display = 'none';
                            } else {
                                badge.style.display = 'inline-block';
                            }
                        }
                        lastNotificationCount = data.count;
                    }
                })
                .catch(error => console.log('Error checking notifications:', error));
        }

        // Request notification permission
        function requestNotificationPermission() {
            if ('Notification' in window) {
                if (Notification.permission === 'default') {
                    Notification.requestPermission().then(function(permission) {
                        if (permission === 'granted') {
                            console.log('Notification permission granted');
                            // Show test notification
                            new Notification('{{ __("messages.notifications_enabled") }}', {
                                body: '{{ __("messages.you_will_receive_notifications") }}',
                                icon: '{{ asset("images/logo.png") }}'
                            });
                        } else if (permission === 'denied') {
                            console.log('Notification permission denied');
                        }
                    });
                } else if (Notification.permission === 'granted') {
                    console.log('Notification permission already granted');
                }
            } else {
                console.log('Browser does not support notifications');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Request permission after 3 seconds
            setTimeout(requestNotificationPermission, 3000);
            
            // Set initial count
            lastNotificationCount = parseInt(document.getElementById('notificationCount')?.textContent) || 0;
            console.log('Initial notification count:', lastNotificationCount);
            
            // Start checking for new notifications every 10 seconds
            setInterval(checkForNewNotifications, 10000);
        });
    </script>

    <!-- TinyMCE Scripts -->
    <script>
        $(document).ready(function() {
            // Initialize TinyMCE for textarea elements
            tinymce.init({
                selector: 'textarea.rich-text',
                height: 200,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                setup: function(editor) {
                    editor.on('init', function() {
                        var textarea = document.getElementById(editor.id);
                        if (textarea) {
                            textarea.removeAttribute('required');
                        }
                    });
                }
            });

            // Handle form submission
            $('form').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                var isValid = true;
                var firstInvalidField = null;
                tinymce.triggerSave();

                $('textarea.rich-text').each(function() {
                    var editorId = $(this).attr('id');
                    var editor = tinymce.get(editorId);

                    if (editor) {
                        var content = editor.getContent({format: 'text'}).trim();
                        var textarea = $(this);

                        if (textarea.prop('required') || textarea.hasClass('required')) {
                            if (content === '' || content.length === 0) {
                                isValid = false;
                                textarea.addClass('is-invalid');
                                var errorDiv = textarea.next('.invalid-feedback');
                                if (errorDiv.length === 0) {
                                    textarea.after('<div class="invalid-feedback">This field is required.</div>');
                                }
                                if (!firstInvalidField) {
                                    firstInvalidField = editor;
                                }
                            } else {
                                textarea.removeClass('is-invalid');
                                textarea.next('.invalid-feedback').remove();
                            }
                        }
                    }
                });

                if (isValid) {
                    form.submit();
                } else {
                    if (firstInvalidField) {
                        firstInvalidField.focus();
                    }
                    if ($('.alert-danger').length === 0) {
                        $('form').prepend('<div class="alert alert-danger">Please fill in all required fields.</div>');
                    }
                }
            });
        });

        $(document).on('focusin', function(e) {
            if ($(e.target).closest(".tox-tinymce-aux, .moxman-window, .tam-assetmanager-root").length) {
                e.stopImmediatePropagation();
            }
        });
    </script>

    @stack('scripts')
    @yield('script')
    @yield('js')
</body>

</html>