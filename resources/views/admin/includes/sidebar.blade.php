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

                <!-- Dashboard -->
                <li class="nav-header">{{ __('messages.dashboard') }}</li>
                
                @if ($user->can('banner-table') || $user->can('banner-add') || $user->can('banner-edit') || $user->can('banner-delete'))
                    <li class="nav-item">
                        <a href="{{ route('banners.index') }}" class="nav-link {{ request()->routeIs('banners.*') ? 'active' : '' }}">
                            <i class="fas fa-images nav-icon"></i>
                            <p>{{ __('messages.Banners') }}</p>
                        </a>
                    </li>
                @endif

                <!-- Users Management -->
                <li class="nav-header">{{ __('messages.users_management') }}</li>

                <li class="nav-item">
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-users nav-icon"></i>
                        <p>{{ __('messages.users') }}</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('families.index') }}" class="nav-link {{ request()->routeIs('families.*') ? 'active' : '' }}">
                        <i class="fas fa-user-friends nav-icon"></i>
                        <p>{{ __('messages.families') }}</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('doctors.index') }}" class="nav-link {{ request()->routeIs('doctors.*') ? 'active' : '' }}">
                        <i class="fas fa-user-md nav-icon"></i>
                        <p>{{ __('messages.doctors') }}</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('nurses.index') }}" class="nav-link {{ request()->routeIs('nurses.*') ? 'active' : '' }}">
                        <i class="fas fa-user-nurse nav-icon"></i>
                        <p>{{ __('messages.nurses') }}</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('super-nurses.index') }}" class="nav-link {{ request()->routeIs('super-nurses.*') ? 'active' : '' }}">
                        <i class="fas fa-user-nurse nav-icon"></i>
                        <p>{{ __('messages.super_nurses') }}</p>
                    </a>
                </li>

                <!-- Medical Services -->
                <li class="nav-header">{{ __('messages.medical_services') }}</li>

                <!-- Appointments & Reservations -->
                <li class="nav-item {{ request()->is('appointments*') || request()->is('appointment-providers*') || request()->is('showers*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('appointments*') || request()->is('appointment-providers*') || request()->is('showers*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            {{ __('messages.appointments_reservations') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('appointments.index') }}" class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                                <i class="fas fa-calendar-check nav-icon"></i>
                                <p>{{ __('messages.all_appointments') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('appointment-providers.index') }}" class="nav-link {{ request()->routeIs('appointment-providers.*') ? 'active' : '' }}">
                                <i class="fas fa-user-clock nav-icon"></i>
                                <p>{{ __('messages.appointment_providers') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('showers.index') }}" class="nav-link {{ request()->routeIs('showers.*') ? 'active' : '' }}">
                                <i class="fas fa-shower nav-icon"></i>
                                <p>{{ __('messages.shower_appointments') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('transfer-patients.index') }}" class="nav-link {{ request()->routeIs('transfer-patients.*') ? 'active' : '' }}">
                                <i class="fas fa-hospital-user nav-icon"></i>
                                <p>{{ __('messages.transfer_patients_list') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Service Types Management -->
                <li class="nav-item {{ request()->is('elderly-cares*') || request()->is('request-nurses*') || request()->is('home-xrays*') || request()->is('medical-tests*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('elderly-cares*') || request()->is('request-nurses*') || request()->is('home-xrays*') || request()->is('medical-tests*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-hospital-user"></i>
                        <p>
                            {{ __('messages.service_types_management') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('request-nurses.index') }}" class="nav-link {{ request()->routeIs('request-nurses.*') ? 'active' : '' }}">
                                <i class="fas fa-stethoscope nav-icon"></i>
                                <p>{{ __('messages.request_nurses_types') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('elderly-cares.index') }}" class="nav-link {{ request()->routeIs('elderly-cares.*') ? 'active' : '' }}">
                                <i class="fas fa-hands-helping nav-icon"></i>
                                <p>{{ __('messages.elderly_care_types') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('home-xrays.index') }}" class="nav-link {{ request()->routeIs('home-xrays.*') ? 'active' : '' }}">
                                <i class="fas fa-x-ray nav-icon"></i>
                                <p>{{ __('messages.home_xray_types') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('medical-tests.index') }}" class="nav-link {{ request()->routeIs('medical-tests.*') ? 'active' : '' }}">
                                <i class="fas fa-vial nav-icon"></i>
                                <p>{{ __('messages.medical_test_types') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Medical Resources -->
                <li class="nav-item {{ request()->is('rooms*') || request()->is('medications*') || request()->is('report-templates*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('rooms*') || request()->is('medications*') || request()->is('report-templates*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clinic-medical"></i>
                        <p>
                            {{ __('messages.medical_resources') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('rooms.index') }}" class="nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                                <i class="fas fa-door-open nav-icon"></i>
                                <p>{{ __('messages.rooms') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('medications.index') }}" class="nav-link {{ request()->routeIs('medications.*') ? 'active' : '' }}">
                                <i class="fas fa-pills nav-icon"></i>
                                <p>{{ __('messages.medications') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('report-templates.index') }}" class="nav-link {{ request()->routeIs('report-templates.*') ? 'active' : '' }}">
                                <i class="fas fa-file-medical-alt nav-icon"></i>
                                <p>{{ __('messages.report_templates') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Medical Forms -->
                <li class="nav-item {{ request()->is('pledge-forms*') || request()->is('special-medical-forms*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('pledge-forms*') || request()->is('special-medical-forms*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-contract"></i>
                        <p>
                            {{ __('messages.medical_forms') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('pledge-forms.index') }}" class="nav-link {{ request()->routeIs('pledge-forms.*') ? 'active' : '' }}">
                                <i class="fas fa-file-signature nav-icon"></i>
                                <p>{{ __('messages.pledge_forms') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('special-medical-forms.index') }}" class="nav-link {{ request()->routeIs('special-medical-forms.*') ? 'active' : '' }}">
                                <i class="fas fa-file-medical nav-icon"></i>
                                <p>{{ __('messages.special_medical_forms') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- E-Commerce -->
                <li class="nav-header">{{ __('messages.ecommerce') }}</li>

                <!-- Catalog Management -->
                <li class="nav-item {{ request()->is('brands*') || request()->is('categories*') || request()->is('products*') || request()->is('deliveries*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('brands*') || request()->is('categories*') || request()->is('products*') || request()->is('deliveries*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>
                            {{ __('messages.Catalog_Management') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('brands.index') }}" class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}">
                                <i class="fas fa-certificate nav-icon"></i>
                                <p>{{ __('messages.Brands') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                <i class="fas fa-th-large nav-icon"></i>
                                <p>{{ __('messages.Categories') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                <i class="fas fa-box-open nav-icon"></i>
                                <p>{{ __('messages.Products') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('deliveries.index') }}" class="nav-link {{ request()->routeIs('deliveries.*') ? 'active' : '' }}">
                                <i class="fas fa-shipping-fast nav-icon"></i>
                                <p>{{ __('messages.Deliveries') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Provider Management -->
                <li class="nav-item {{ request()->is('types*') || request()->is('provider-categories*') || request()->is('providers*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('types*') || request()->is('provider-categories*') || request()->is('providers*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-handshake"></i>
                        <p>
                            {{ __('messages.Provider Management') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('types.index') }}" class="nav-link {{ request()->routeIs('types.*') ? 'active' : '' }}">
                                <i class="fas fa-list-ul nav-icon"></i>
                                <p>{{ __('messages.types') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('provider-categories.index') }}" class="nav-link {{ request()->routeIs('provider-categories.*') ? 'active' : '' }}">
                                <i class="fas fa-layer-group nav-icon"></i>
                                <p>{{ __('messages.provider-categories') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('providers.index') }}" class="nav-link {{ request()->routeIs('providers.*') ? 'active' : '' }}">
                                <i class="fas fa-building nav-icon"></i>
                                <p>{{ __('messages.providers') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                @canany(['order-table', 'order-add', 'order-edit', 'order-delete'])
                    <li class="nav-item">
                        <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart nav-icon"></i>
                            <p>{{ __('messages.orders') }}</p>
                        </a>
                    </li>
                @endcanany

                <!-- Financial Management -->
                <li class="nav-header">{{ __('messages.financial_management') }}</li>

                @if ($user->can('pos-table') || $user->can('pos-add') || $user->can('pos-edit') || $user->can('pos-delete'))
                    <li class="nav-item">
                        <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                            <i class="fas fa-cash-register nav-icon"></i>
                            <p>{{ __('messages.pos_list') }}</p>
                        </a>
                    </li>
                @endif

                @if ($user->can('card-table') || $user->can('card-add') || $user->can('card-edit') || $user->can('card-delete'))
                    <li class="nav-item">
                        <a href="{{ route('cards.index') }}" class="nav-link {{ request()->routeIs('cards.*') ? 'active' : '' }}">
                            <i class="fas fa-credit-card nav-icon"></i>
                            <p>{{ __('messages.cards_list') }}</p>
                        </a>
                    </li>
                @endif

                <!-- Content Management -->
                <li class="nav-header">{{ __('messages.content_management') }}</li>

                <li class="nav-item">
                    <a href="{{ route('pages.index') }}" class="nav-link {{ request()->routeIs('pages.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <p>{{ __('messages.pages_management') }}</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('news.index') }}" class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}">
                        <i class="fas fa-newspaper nav-icon"></i>
                        <p>{{ __('messages.news') }}</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('careers.index') }}" class="nav-link {{ request()->routeIs('careers.*') ? 'active' : '' }}">
                        <i class="fas fa-briefcase nav-icon"></i>
                        <p>{{ __('messages.careers') }}</p>
                    </a>
                </li>

                <!-- System Settings -->
                <li class="nav-header">{{ __('messages.system_settings') }}</li>

                <li class="nav-item">
                    <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cogs nav-icon"></i>
                        <p>{{ __('messages.Settings') }}</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('app-configs.index') }}" class="nav-link {{ request()->routeIs('app-configs.*') ? 'active' : '' }}">
                        <i class="fas fa-mobile-alt nav-icon"></i>
                        <p>{{ __('messages.app_configurations') }}</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('notifications.create') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                        <i class="fas fa-bell nav-icon"></i>
                        <p>{{ __('messages.notifications') }}</p>
                    </a>
                </li>

                <!-- Admin Management -->
                <li class="nav-header">{{ __('messages.admin_management') }}</li>

                <li class="nav-item">
                    <a href="{{ route('admin.login.edit', auth()->user()->id) }}" class="nav-link {{ request()->routeIs('admin.login.edit') ? 'active' : '' }}">
                        <i class="fas fa-user-circle nav-icon"></i>
                        <p>{{ __('messages.Admin_account') }}</p>
                    </a>
                </li>

                @if ($user->can('role-table') || $user->can('role-add') || $user->can('role-edit') || $user->can('role-delete'))
                    <li class="nav-item">
                        <a href="{{ route('admin.role.index') }}" class="nav-link {{ request()->routeIs('admin.role.*') ? 'active' : '' }}">
                            <i class="fas fa-shield-alt nav-icon"></i>
                            <p>{{ __('messages.Roles') }}</p>
                        </a>
                    </li>
                @endif

                @if ($user->can('employee-table') || $user->can('employee-add') || $user->can('employee-edit') || $user->can('employee-delete'))
                    <li class="nav-item">
                        <a href="{{ route('admin.employee.index') }}" class="nav-link {{ request()->routeIs('admin.employee.*') ? 'active' : '' }}">
                            <i class="fas fa-user-tie nav-icon"></i>
                            <p>{{ __('messages.Employee') }}</p>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>