<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{ asset('assets/admin/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Green Care</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('assets/admin/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                    alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                @if ($user->can('banner-table') || $user->can('banner-add') || $user->can('banner-edit') || $user->can('banner-delete'))
                    <li class="nav-item">
                        <a href="{{ route('banners.index') }}" class="nav-link">
                            <i class="fas fa-images nav-icon"></i>
                            <p> {{ __('messages.Banners') }} </p>
                        </a>
                    </li>
                @endif

                <!-- User Management Section -->
                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <i class="fas fa-users nav-icon"></i>
                        <p> {{ __('messages.users') }} </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('families.index') }}" class="nav-link">
                        <i class="fas fa-home nav-icon"></i>
                        <p> {{ __('messages.families') }} </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('doctors.index') }}" class="nav-link">
                        <i class="fas fa-user-md nav-icon"></i>
                        <p> {{ __('messages.doctors') }} </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('nurses.index') }}" class="nav-link">
                        <i class="fas fa-user-nurse nav-icon"></i>
                        <p> {{ __('messages.nurses') }} </p>
                    </a>
                </li>

                <!-- Medical Management -->
                <li class="nav-item">
                    <a href="{{ route('report-templates.index') }}" class="nav-link">
                        <i class="fas fa-file-medical nav-icon"></i>
                        <p> {{ __('messages.report_templates') }} </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('rooms.index') }}" class="nav-link">
                        <i class="fas fa-bed nav-icon"></i>
                        <p> {{ __('messages.rooms') }} </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('medications.index') }}" class="nav-link">
                        <i class="fas fa-pills nav-icon"></i>
                        <p> {{ __('messages.medications') }} </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pledge-forms.index') }}" class="nav-link">
                        <i class="fas fa-file-signature nav-icon"></i>
                        <p> {{ __('messages.pledge_forms') }} </p>
                    </a>
                </li>

                <!-- Service Types Management -->
                <li class="nav-item {{ request()->is('elderly-cares*') || request()->is('request-nurses*') || request()->is('home-xrays*') || request()->is('medical-tests*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>
                            {{ __('messages.service_types_management') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('request-nurses.index') }}"
                                class="nav-link {{ request()->routeIs('request-nurses.*') ? 'active' : '' }}">
                                <i class="fas fa-heart nav-icon"></i>
                                <p>{{ __('messages.request_nurses_types') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('elderly-cares.index') }}"
                                class="nav-link {{ request()->routeIs('elderly-cares.*') ? 'active' : '' }}">
                                <i class="fas fa-heart nav-icon"></i>
                                <p>{{ __('messages.elderly_care_types') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('home-xrays.index') }}"
                                class="nav-link {{ request()->routeIs('home-xrays.*') ? 'active' : '' }}">
                                <i class="fas fa-x-ray nav-icon"></i>
                                <p>{{ __('messages.home_xray_types') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('medical-tests.index') }}"
                                class="nav-link {{ request()->routeIs('medical-tests.*') ? 'active' : '' }}">
                                <i class="fas fa-flask nav-icon"></i>
                                <p>{{ __('messages.medical_test_types') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Appointments Management -->
                <li class="nav-item">
                    <a href="{{ route('appointments.index') }}" class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check nav-icon"></i>
                        <p> {{ __('messages.all_appointments') }} </p>
                    </a>
                </li>
              
                <li class="nav-item">
                    <a href="{{ route('appointment-providers.index') }}" class="nav-link {{ request()->routeIs('appointment-providers.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check nav-icon"></i>
                        <p> {{ __('messages.appointment_providers') }} </p>
                    </a>
                </li>

                <!-- Catalog Management (Existing Section) -->
                <li class="nav-item {{ request()->is('deliveries*') || request()->is('categories*') || request()->is('products*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>
                            {{ __('messages.Catalog_Management') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('brands.index') }}"
                                class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>{{ __('messages.Brands') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}"
                                class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                <i class="fas fa-folder nav-icon"></i>
                                <p>{{ __('messages.Categories') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}"
                                class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                <i class="fas fa-box nav-icon"></i>
                                <p>{{ __('messages.Products') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('deliveries.index') }}"
                                class="nav-link {{ request()->routeIs('deliveries.*') ? 'active' : '' }}">
                                <i class="fas fa-truck nav-icon"></i>
                                <p>{{ __('messages.Deliveries') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Provider Management (Existing Section) -->
                <li class="nav-item {{ request()->is('types*') || request()->is('provider-categories*') || request()->is('providers*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>
                            {{ __('messages.Provider Management') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('types.index') }}"
                                class="nav-link {{ request()->routeIs('types.*') ? 'active' : '' }}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>{{ __('messages.types') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('provider-categories.index') }}"
                                class="nav-link {{ request()->routeIs('provider-categories.*') ? 'active' : '' }}">
                                <i class="fas fa-folder nav-icon"></i>
                                <p>{{ __('messages.provider-categories') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('providers.index') }}"
                                class="nav-link {{ request()->routeIs('providers.*') ? 'active' : '' }}">
                                <i class="fas fa-box nav-icon"></i>
                                <p>{{ __('messages.providers') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                @canany(['order-table', 'order-add', 'order-edit', 'order-delete'])
                    <li class="nav-item">
                        <a href="{{ route('orders.index') }}"
                            class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                            <i class="fas fa-handshake nav-icon"></i>
                            <p>{{ __('messages.orders') }}</p>
                        </a>
                    </li>
                @endcanany

                <!-- Content Management -->
                <li class="nav-item">
                    <a href="{{ route('pages.index') }}" class="nav-link">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <p> {{ __('messages.pages_management') }} </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('news.index') }}" class="nav-link">
                        <i class="fas fa-bullhorn nav-icon"></i>
                        <p> {{ __('messages.news') }} </p>
                    </a>
                </li>

                <!-- System Configuration -->
                <li class="nav-item">
                    <a href="{{ route('settings.index') }}" class="nav-link">
                        <i class="fas fa-cog nav-icon"></i>
                        <p>{{ __('messages.Settings') }} </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('app-configs.index') }}"
                        class="nav-link {{ request()->routeIs('app-configs.index') ? 'active' : '' }}">
                        <i class="fas fa-wrench nav-icon"></i>
                        <p>{{ __('messages.app_configurations') }}</p>
                    </a>
                </li>
          
                <li class="nav-item">
                    <a href="{{ route('notifications.create') }}"
                        class="nav-link {{ request()->routeIs('notifications.create') ? 'active' : '' }}">
                        <i class="fas fa-wrench nav-icon"></i>
                        <p>{{ __('messages.notifications') }}</p>
                    </a>
                </li>

                <!-- User Management -->
                <li class="nav-header">{{ __('messages.user_management') }}</li>

                <li class="nav-item">
                    <a href="{{ route('admin.login.edit', auth()->user()->id) }}" class="nav-link">
                        <i class="fas fa-user nav-icon"></i>
                        <p>{{ __('messages.Admin_account') }} </p>
                    </a>
                </li>

                @if ($user->can('role-table') || $user->can('role-add') || $user->can('role-edit') || $user->can('role-delete'))
                    <li class="nav-item">
                        <a href="{{ route('admin.role.index') }}" class="nav-link">
                            <i class="fas fa-user-shield nav-icon"></i>
                            <span>{{ __('messages.Roles') }} </span>
                        </a>
                    </li>
                @endif

                @if (
                    $user->can('employee-table') ||
                        $user->can('employee-add') ||
                        $user->can('employee-edit') ||
                        $user->can('employee-delete'))
                    <li class="nav-item">
                        <a href="{{ route('admin.employee.index') }}" class="nav-link">
                            <i class="fas fa-users nav-icon"></i>
                            <span> {{ __('messages.Employee') }} </span>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>