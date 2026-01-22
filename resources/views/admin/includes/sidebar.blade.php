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

                @canany(['banner-table', 'banner-add', 'banner-edit', 'banner-delete'])
                    <li class="nav-item">
                        <a href="{{ route('banners.index') }}"
                            class="nav-link {{ request()->routeIs('banners.*') ? 'active' : '' }}">
                            <i class="fas fa-images nav-icon"></i>
                            <p>{{ __('messages.Banners') }}</p>
                        </a>
                    </li>
                @endcanany

                <!-- Users Management -->
                @canany(['user-table', 'family-table', 'doctor-table', 'nurse-table'])
                    <li class="nav-header">{{ __('messages.users_management') }}</li>
                @endcanany

                @canany(['user-table', 'user-add', 'user-edit', 'user-delete'])
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}"
                            class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="fas fa-users nav-icon"></i>
                            <p>{{ __('messages.users') }}</p>
                        </a>
                    </li>
                @endcanany

                @canany(['family-table', 'family-add', 'family-edit', 'family-delete'])
                    <li class="nav-item">
                        <a href="{{ route('families.index') }}"
                            class="nav-link {{ request()->routeIs('families.*') ? 'active' : '' }}">
                            <i class="fas fa-user-friends nav-icon"></i>
                            <p>{{ __('messages.families') }}</p>
                        </a>
                    </li>
                @endcanany

                @canany(['doctor-table', 'doctor-add', 'doctor-edit', 'doctor-delete'])
                    <li class="nav-item">
                        <a href="{{ route('doctors.index') }}"
                            class="nav-link {{ request()->routeIs('doctors.*') ? 'active' : '' }}">
                            <i class="fas fa-user-md nav-icon"></i>
                            <p>{{ __('messages.doctors') }}</p>
                        </a>
                    </li>
                @endcanany

                @canany(['nurse-table', 'nurse-add', 'nurse-edit', 'nurse-delete'])
                    <li class="nav-item">
                        <a href="{{ route('nurses.index') }}"
                            class="nav-link {{ request()->routeIs('nurses.*') ? 'active' : '' }}">
                            <i class="fas fa-user-nurse nav-icon"></i>
                            <p>{{ __('messages.nurses') }}</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('super-nurses.index') }}"
                            class="nav-link {{ request()->routeIs('super-nurses.*') ? 'active' : '' }}">
                            <i class="fas fa-user-nurse nav-icon"></i>
                            <p>{{ __('messages.super_nurses') }}</p>
                        </a>
                    </li>
                @endcanany

                <!-- Medical Services -->
                @canany(['appointment-table', 'appointmentProvider-table', 'shower-table', 'transferPatient-table', 'typeRequestNurse-table', 'typeElderlyCare-table', 'typeHomeXray-table', 'typeMedicalTest-table', 'room-table', 'report-template-table', 'pledgeForm-table', 'specialMedicalForm-table'])
                    <li class="nav-header">{{ __('messages.medical_services') }}</li>
                @endcanany

                <!-- Appointments & Reservations -->
                @canany(['appointment-table', 'appointmentProvider-table', 'shower-table', 'transferPatient-table'])
                    <li
                        class="nav-item {{ request()->is('appointments*') || request()->is('appointment-providers*') || request()->is('showers*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->is('appointments*') || request()->is('appointment-providers*') || request()->is('showers*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>
                                {{ __('messages.appointments_reservations') }}
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @canany(['appointment-table', 'appointment-add', 'appointment-edit', 'appointment-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('appointments.index') }}"
                                        class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                                        <i class="fas fa-calendar-check nav-icon"></i>
                                        <p>{{ __('messages.all_appointments') }}</p>
                                    </a>
                                </li>
                            @endcanany
                            @canany(['appointmentProvider-table', 'appointmentProvider-add', 'appointmentProvider-edit', 'appointmentProvider-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('appointment-providers.index') }}"
                                        class="nav-link {{ request()->routeIs('appointment-providers.*') ? 'active' : '' }}">
                                        <i class="fas fa-user-clock nav-icon"></i>
                                        <p>{{ __('messages.appointment_providers') }}</p>
                                    </a>
                                </li>
                            @endcanany
                            @canany(['shower-table', 'shower-add', 'shower-edit', 'shower-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('showers.index') }}"
                                        class="nav-link {{ request()->routeIs('showers.*') ? 'active' : '' }}">
                                        <i class="fas fa-shower nav-icon"></i>
                                        <p>{{ __('messages.shower_appointments') }}</p>
                                    </a>
                                </li>
                            @endcanany
                            @canany(['transferPatient-table', 'transferPatient-add', 'transferPatient-edit', 'transferPatient-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('transfer-patients.index') }}"
                                        class="nav-link {{ request()->routeIs('transfer-patients.*') ? 'active' : '' }}">
                                        <i class="fas fa-hospital-user nav-icon"></i>
                                        <p>{{ __('messages.transfer_patients_list') }}</p>
                                    </a>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                <!-- Service Types Management -->
                @canany(['typeRequestNurse-table', 'typeElderlyCare-table', 'typeHomeXray-table', 'typeMedicalTest-table'])
                    <li
                        class="nav-item {{ request()->is('elderly-cares*') || request()->is('request-nurses*') || request()->is('home-xrays*') || request()->is('medical-tests*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->is('elderly-cares*') || request()->is('request-nurses*') || request()->is('home-xrays*') || request()->is('medical-tests*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-hospital-user"></i>
                            <p>
                                {{ __('messages.service_types_management') }}
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @canany(['typeRequestNurse-table', 'typeRequestNurse-add', 'typeRequestNurse-edit', 'typeRequestNurse-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('request-nurses.index') }}"
                                        class="nav-link {{ request()->routeIs('request-nurses.*') ? 'active' : '' }}">
                                        <i class="fas fa-stethoscope nav-icon"></i>
                                        <p>{{ __('messages.request_nurses_types') }}</p>
                                    </a>
                                </li>
                            @endcanany
                            @canany(['typeElderlyCare-table', 'typeElderlyCare-add', 'typeElderlyCare-edit', 'typeElderlyCare-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('elderly-cares.index') }}"
                                        class="nav-link {{ request()->routeIs('elderly-cares.*') ? 'active' : '' }}">
                                        <i class="fas fa-hands-helping nav-icon"></i>
                                        <p>{{ __('messages.elderly_care_types') }}</p>
                                    </a>
                                </li>
                            @endcanany
                            @canany(['typeHomeXray-table', 'typeHomeXray-add', 'typeHomeXray-edit', 'typeHomeXray-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('home-xrays.index') }}"
                                        class="nav-link {{ request()->routeIs('home-xrays.*') ? 'active' : '' }}">
                                        <i class="fas fa-x-ray nav-icon"></i>
                                        <p>{{ __('messages.home_xray_types') }}</p>
                                    </a>
                                </li>
                            @endcanany
                            @canany(['typeMedicalTest-table', 'typeMedicalTest-add', 'typeMedicalTest-edit', 'typeMedicalTest-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('medical-tests.index') }}"
                                        class="nav-link {{ request()->routeIs('medical-tests.*') ? 'active' : '' }}">
                                        <i class="fas fa-vial nav-icon"></i>
                                        <p>{{ __('messages.medical_test_types') }}</p>
                                    </a>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                <!-- Medical Resources -->
                @canany(['room-table', 'report-template-table'])
                    <li
                        class="nav-item {{ request()->is('rooms*') || request()->is('medications*') || request()->is('report-templates*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->is('rooms*') || request()->is('medications*') || request()->is('report-templates*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clinic-medical"></i>
                            <p>
                                {{ __('messages.medical_resources') }}
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @canany(['room-table', 'room-add', 'room-edit', 'room-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('rooms.index') }}"
                                        class="nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                                        <i class="fas fa-door-open nav-icon"></i>
                                        <p>{{ __('messages.rooms') }}</p>
                                    </a>
                                </li>
                            @endcanany
                            <li class="nav-item">
                                <a href="{{ route('medications.index') }}"
                                    class="nav-link {{ request()->routeIs('medications.*') ? 'active' : '' }}">
                                    <i class="fas fa-pills nav-icon"></i>
                                    <p>{{ __('messages.medications') }}</p>
                                </a>
                            </li>
                            @canany(['report-template-table', 'report-template-add', 'report-template-edit', 'report-template-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('report-templates.index') }}"
                                        class="nav-link {{ request()->routeIs('report-templates.*') ? 'active' : '' }}">
                                        <i class="fas fa-file-medical-alt nav-icon"></i>
                                        <p>{{ __('messages.report_templates') }}</p>
                                    </a>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                <!-- Medical Forms -->
                @canany(['pledgeForm-table', 'specialMedicalForm-table'])
                    <li
                        class="nav-item {{ request()->is('pledge-forms*') || request()->is('special-medical-forms*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->is('pledge-forms*') || request()->is('special-medical-forms*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-contract"></i>
                            <p>
                                {{ __('messages.medical_forms') }}
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @canany(['pledgeForm-table', 'pledgeForm-add', 'pledgeForm-edit', 'pledgeForm-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('pledge-forms.index') }}"
                                        class="nav-link {{ request()->routeIs('pledge-forms.*') ? 'active' : '' }}">
                                        <i class="fas fa-file-signature nav-icon"></i>
                                        <p>{{ __('messages.pledge_forms') }}</p>
                                    </a>
                                </li>
                            @endcanany
                            @canany(['specialMedicalForm-table', 'specialMedicalForm-add', 'specialMedicalForm-edit', 'specialMedicalForm-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('special-medical-forms.index') }}"
                                        class="nav-link {{ request()->routeIs('special-medical-forms.*') ? 'active' : '' }}">
                                        <i class="fas fa-file-medical nav-icon"></i>
                                        <p>{{ __('messages.special_medical_forms') }}</p>
                                    </a>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                <!-- E-Commerce -->
                @canany(['brand-table', 'category-table', 'product-table', 'delivery-table', 'type-table', 'providerCategory-table', 'provider-table', 'order-table'])
                    <li class="nav-header">{{ __('messages.ecommerce') }}</li>
                @endcanany

                <!-- Catalog Management -->
                @canany(['brand-table', 'category-table', 'product-table', 'delivery-table'])
                    <li
                        class="nav-item {{ request()->is('brands*') || request()->is('categories*') || request()->is('products*') || request()->is('deliveries*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->is('brands*') || request()->is('categories*') || request()->is('products*') || request()->is('deliveries*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-store"></i>
                            <p>
                                {{ __('messages.Catalog_Management') }}
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @canany(['brand-table', 'brand-add', 'brand-edit', 'brand-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('brands.index') }}"
                                        class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}">
                                        <i class="fas fa-certificate nav-icon"></i>
                                        <p>{{ __('messages.Brands') }}</p>
                                    </a>
                                </li>
                            @endcanany
                            @canany(['category-table', 'category-add', 'category-edit', 'category-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('categories.index') }}"
                                        class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                        <i class="fas fa-th-large nav-icon"></i>
                                        <p>{{ __('messages.Categories') }}</p>
                                    </a>
                                </li>
                            @endcanany
                            @canany(['product-table', 'product-add', 'product-edit', 'product-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('products.index') }}"
                                        class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                                        <i class="fas fa-box-open nav-icon"></i>
                                        <p>{{ __('messages.Products') }}</p>
                                    </a>
                                </li>
                            @endcanany
                            @canany(['delivery-table', 'delivery-add', 'delivery-edit', 'delivery-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('deliveries.index') }}"
                                        class="nav-link {{ request()->routeIs('deliveries.*') ? 'active' : '' }}">
                                        <i class="fas fa-shipping-fast nav-icon"></i>
                                        <p>{{ __('messages.Deliveries') }}</p>
                                    </a>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                <!-- Provider Management -->
                @canany(['type-table', 'providerCategory-table', 'provider-table'])
                    <li
                        class="nav-item {{ request()->is('types*') || request()->is('provider-categories*') || request()->is('providers*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->is('types*') || request()->is('provider-categories*') || request()->is('providers*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-handshake"></i>
                            <p>
                                {{ __('messages.Provider Management') }}
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @canany(['type-table', 'type-add', 'type-edit', 'type-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('types.index') }}"
                                        class="nav-link {{ request()->routeIs('types.*') ? 'active' : '' }}">
                                        <i class="fas fa-list-ul nav-icon"></i>
                                        <p>{{ __('messages.types') }}</p>
                                    </a>
                                </li>
                            @endcanany
                            @canany(['providerCategory-table', 'providerCategory-add', 'providerCategory-edit', 'providerCategory-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('provider-categories.index') }}"
                                        class="nav-link {{ request()->routeIs('provider-categories.*') ? 'active' : '' }}">
                                        <i class="fas fa-layer-group nav-icon"></i>
                                        <p>{{ __('messages.provider-categories') }}</p>
                                    </a>
                                </li>
                            @endcanany
                            @canany(['provider-table', 'provider-add', 'provider-edit', 'provider-delete'])
                                <li class="nav-item">
                                    <a href="{{ route('providers.index') }}"
                                        class="nav-link {{ request()->routeIs('providers.*') ? 'active' : '' }}">
                                        <i class="fas fa-building nav-icon"></i>
                                        <p>{{ __('messages.providers') }}</p>
                                    </a>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                @canany(['order-table', 'order-add', 'order-edit', 'order-delete'])
                    <li class="nav-item">
                        <a href="{{ route('orders.index') }}"
                            class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart nav-icon"></i>
                            <p>{{ __('messages.orders') }}</p>
                        </a>
                    </li>
                @endcanany

                <!-- Financial Management -->
                @canany(['pos-table', 'card-table', 'lab-table'])
                    <li class="nav-header">{{ __('messages.financial_management') }}</li>
                @endcanany

                @canany(['pos-table', 'pos-add', 'pos-edit', 'pos-delete'])
                    <li class="nav-item">
                        <a href="{{ route('pos.index') }}"
                            class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                            <i class="fas fa-cash-register nav-icon"></i>
                            <p>{{ __('messages.pos_list') }}</p>
                        </a>
                    </li>
                @endcanany

                @canany(['card-table', 'card-add', 'card-edit', 'card-delete'])
                    <li class="nav-item">
                        <a href="{{ route('cards.index') }}"
                            class="nav-link {{ request()->routeIs('cards.*') ? 'active' : '' }}">
                            <i class="fas fa-credit-card nav-icon"></i>
                            <p>{{ __('messages.cards_list') }}</p>
                        </a>
                    </li>
                @endcanany

                @canany(['lab-table', 'lab-add', 'lab-edit', 'lab-delete'])
                    <li class="nav-item">
                        <a href="{{ route('labs.index') }}"
                            class="nav-link {{ request()->routeIs('labs.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-hospital"></i>
                            <p>{{ __('messages.Labs') }}</p>
                        </a>
                    </li>
                @endcanany

                <!-- Content Management -->
                @canany(['page-table', 'news-table', 'career-table'])
                    <li class="nav-header">{{ __('messages.content_management') }}</li>
                @endcanany

                @canany(['page-table', 'page-add', 'page-edit', 'page-delete'])
                    <li class="nav-item">
                        <a href="{{ route('pages.index') }}"
                            class="nav-link {{ request()->routeIs('pages.*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt nav-icon"></i>
                            <p>{{ __('messages.pages_management') }}</p>
                        </a>
                    </li>
                @endcanany

                @canany(['news-table', 'news-add', 'news-edit', 'news-delete'])
                    <li class="nav-item">
                        <a href="{{ route('news.index') }}"
                            class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}">
                            <i class="fas fa-newspaper nav-icon"></i>
                            <p>{{ __('messages.news') }}</p>
                        </a>
                    </li>
                @endcanany

                @canany(['career-table', 'career-add', 'career-edit', 'career-delete'])
                    <li class="nav-item">
                        <a href="{{ route('careers.index') }}"
                            class="nav-link {{ request()->routeIs('careers.*') ? 'active' : '' }}">
                            <i class="fas fa-briefcase nav-icon"></i>
                            <p>{{ __('messages.careers') }}</p>
                        </a>
                    </li>
                @endcanany

                <!-- System Settings -->
                @canany(['setting-table', 'appConfig-table', 'notification-table'])
                    <li class="nav-header">{{ __('messages.system_settings') }}</li>
                @endcanany

                @canany(['setting-table', 'setting-add', 'setting-edit', 'setting-delete'])
                    <li class="nav-item">
                        <a href="{{ route('settings.index') }}"
                            class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="fas fa-cogs nav-icon"></i>
                            <p>{{ __('messages.Settings') }}</p>
                        </a>
                    </li>
                @endcanany

                @canany(['appConfig-table', 'appConfig-add', 'appConfig-edit', 'appConfig-delete'])
                    <li class="nav-item">
                        <a href="{{ route('app-configs.index') }}"
                            class="nav-link {{ request()->routeIs('app-configs.*') ? 'active' : '' }}">
                            <i class="fas fa-mobile-alt nav-icon"></i>
                            <p>{{ __('messages.app_configurations') }}</p>
                        </a>
                    </li>
                @endcanany

                @canany(['notification-table', 'notification-add', 'notification-edit', 'notification-delete'])
                    <li class="nav-item">
                        <a href="{{ route('notifications.create') }}"
                            class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                            <i class="fas fa-bell nav-icon"></i>
                            <p>{{ __('messages.notifications') }}</p>
                        </a>
                    </li>
                @endcanany

                <!-- Admin Management -->
                <li class="nav-header">{{ __('messages.admin_management') }}</li>

                <li class="nav-item">
                    <a href="{{ route('admin.login.edit', auth()->user()->id) }}"
                        class="nav-link {{ request()->routeIs('admin.login.edit') ? 'active' : '' }}">
                        <i class="fas fa-user-circle nav-icon"></i>
                        <p>{{ __('messages.Admin_account') }}</p>
                    </a>
                </li>

                @canany(['role-table', 'role-add', 'role-edit', 'role-delete'])
                    <li class="nav-item">
                        <a href="{{ route('admin.role.index') }}"
                            class="nav-link {{ request()->routeIs('admin.role.*') ? 'active' : '' }}">
                            <i class="fas fa-shield-alt nav-icon"></i>
                            <p>{{ __('messages.Roles') }}</p>
                        </a>
                    </li>
                @endcanany

                @canany(['employee-table', 'employee-add', 'employee-edit', 'employee-delete'])
                    <li class="nav-item">
                        <a href="{{ route('admin.employee.index') }}"
                            class="nav-link {{ request()->routeIs('admin.employee.*') ? 'active' : '' }}">
                            <i class="fas fa-user-tie nav-icon"></i>
                            <p>{{ __('messages.Employee') }}</p>
                        </a>
                    </li>
                @endcanany

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
