<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="<?php echo e(asset('assets/admin/dist/img/AdminLTELogo.png')); ?>" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Green Care</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo e(asset('assets/admin/dist/img/user2-160x160.jpg')); ?>" class="img-circle elevation-2"
                    alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo e(auth()->user()->name); ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <!-- Dashboard -->
                <li class="nav-header"><?php echo e(__('messages.dashboard')); ?></li>
                
                <?php if($user->can('banner-table') || $user->can('banner-add') || $user->can('banner-edit') || $user->can('banner-delete')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('banners.index')); ?>" class="nav-link <?php echo e(request()->routeIs('banners.*') ? 'active' : ''); ?>">
                            <i class="fas fa-images nav-icon"></i>
                            <p><?php echo e(__('messages.Banners')); ?></p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Users Management -->
                <li class="nav-header"><?php echo e(__('messages.users_management')); ?></li>

                <li class="nav-item">
                    <a href="<?php echo e(route('users.index')); ?>" class="nav-link <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">
                        <i class="fas fa-users nav-icon"></i>
                        <p><?php echo e(__('messages.users')); ?></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('families.index')); ?>" class="nav-link <?php echo e(request()->routeIs('families.*') ? 'active' : ''); ?>">
                        <i class="fas fa-user-friends nav-icon"></i>
                        <p><?php echo e(__('messages.families')); ?></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('doctors.index')); ?>" class="nav-link <?php echo e(request()->routeIs('doctors.*') ? 'active' : ''); ?>">
                        <i class="fas fa-user-md nav-icon"></i>
                        <p><?php echo e(__('messages.doctors')); ?></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('nurses.index')); ?>" class="nav-link <?php echo e(request()->routeIs('nurses.*') ? 'active' : ''); ?>">
                        <i class="fas fa-user-nurse nav-icon"></i>
                        <p><?php echo e(__('messages.nurses')); ?></p>
                    </a>
                </li>

                <!-- Medical Services -->
                <li class="nav-header"><?php echo e(__('messages.medical_services')); ?></li>

                <!-- Appointments & Reservations -->
                <li class="nav-item <?php echo e(request()->is('appointments*') || request()->is('appointment-providers*') || request()->is('showers*') ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e(request()->is('appointments*') || request()->is('appointment-providers*') || request()->is('showers*') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            <?php echo e(__('messages.appointments_reservations')); ?>

                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('appointments.index')); ?>" class="nav-link <?php echo e(request()->routeIs('appointments.*') ? 'active' : ''); ?>">
                                <i class="fas fa-calendar-check nav-icon"></i>
                                <p><?php echo e(__('messages.all_appointments')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('appointment-providers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('appointment-providers.*') ? 'active' : ''); ?>">
                                <i class="fas fa-user-clock nav-icon"></i>
                                <p><?php echo e(__('messages.appointment_providers')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('showers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('showers.*') ? 'active' : ''); ?>">
                                <i class="fas fa-shower nav-icon"></i>
                                <p><?php echo e(__('messages.shower_appointments')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('transfer-patients.index')); ?>" class="nav-link <?php echo e(request()->routeIs('transfer-patients.*') ? 'active' : ''); ?>">
                                <i class="fas fa-hospital-user nav-icon"></i>
                                <p><?php echo e(__('messages.transfer_patients_list')); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Service Types Management -->
                <li class="nav-item <?php echo e(request()->is('elderly-cares*') || request()->is('request-nurses*') || request()->is('home-xrays*') || request()->is('medical-tests*') ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e(request()->is('elderly-cares*') || request()->is('request-nurses*') || request()->is('home-xrays*') || request()->is('medical-tests*') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-hospital-user"></i>
                        <p>
                            <?php echo e(__('messages.service_types_management')); ?>

                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('request-nurses.index')); ?>" class="nav-link <?php echo e(request()->routeIs('request-nurses.*') ? 'active' : ''); ?>">
                                <i class="fas fa-stethoscope nav-icon"></i>
                                <p><?php echo e(__('messages.request_nurses_types')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('elderly-cares.index')); ?>" class="nav-link <?php echo e(request()->routeIs('elderly-cares.*') ? 'active' : ''); ?>">
                                <i class="fas fa-hands-helping nav-icon"></i>
                                <p><?php echo e(__('messages.elderly_care_types')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('home-xrays.index')); ?>" class="nav-link <?php echo e(request()->routeIs('home-xrays.*') ? 'active' : ''); ?>">
                                <i class="fas fa-x-ray nav-icon"></i>
                                <p><?php echo e(__('messages.home_xray_types')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('medical-tests.index')); ?>" class="nav-link <?php echo e(request()->routeIs('medical-tests.*') ? 'active' : ''); ?>">
                                <i class="fas fa-vial nav-icon"></i>
                                <p><?php echo e(__('messages.medical_test_types')); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Medical Resources -->
                <li class="nav-item <?php echo e(request()->is('rooms*') || request()->is('medications*') || request()->is('report-templates*') ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e(request()->is('rooms*') || request()->is('medications*') || request()->is('report-templates*') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-clinic-medical"></i>
                        <p>
                            <?php echo e(__('messages.medical_resources')); ?>

                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('rooms.index')); ?>" class="nav-link <?php echo e(request()->routeIs('rooms.*') ? 'active' : ''); ?>">
                                <i class="fas fa-door-open nav-icon"></i>
                                <p><?php echo e(__('messages.rooms')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('medications.index')); ?>" class="nav-link <?php echo e(request()->routeIs('medications.*') ? 'active' : ''); ?>">
                                <i class="fas fa-pills nav-icon"></i>
                                <p><?php echo e(__('messages.medications')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('report-templates.index')); ?>" class="nav-link <?php echo e(request()->routeIs('report-templates.*') ? 'active' : ''); ?>">
                                <i class="fas fa-file-medical-alt nav-icon"></i>
                                <p><?php echo e(__('messages.report_templates')); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Medical Forms -->
                <li class="nav-item <?php echo e(request()->is('pledge-forms*') || request()->is('special-medical-forms*') ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e(request()->is('pledge-forms*') || request()->is('special-medical-forms*') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-file-contract"></i>
                        <p>
                            <?php echo e(__('messages.medical_forms')); ?>

                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('pledge-forms.index')); ?>" class="nav-link <?php echo e(request()->routeIs('pledge-forms.*') ? 'active' : ''); ?>">
                                <i class="fas fa-file-signature nav-icon"></i>
                                <p><?php echo e(__('messages.pledge_forms')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('special-medical-forms.index')); ?>" class="nav-link <?php echo e(request()->routeIs('special-medical-forms.*') ? 'active' : ''); ?>">
                                <i class="fas fa-file-medical nav-icon"></i>
                                <p><?php echo e(__('messages.special_medical_forms')); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- E-Commerce -->
                <li class="nav-header"><?php echo e(__('messages.ecommerce')); ?></li>

                <!-- Catalog Management -->
                <li class="nav-item <?php echo e(request()->is('brands*') || request()->is('categories*') || request()->is('products*') || request()->is('deliveries*') ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e(request()->is('brands*') || request()->is('categories*') || request()->is('products*') || request()->is('deliveries*') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-store"></i>
                        <p>
                            <?php echo e(__('messages.Catalog_Management')); ?>

                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('brands.index')); ?>" class="nav-link <?php echo e(request()->routeIs('brands.*') ? 'active' : ''); ?>">
                                <i class="fas fa-certificate nav-icon"></i>
                                <p><?php echo e(__('messages.Brands')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('categories.index')); ?>" class="nav-link <?php echo e(request()->routeIs('categories.*') ? 'active' : ''); ?>">
                                <i class="fas fa-th-large nav-icon"></i>
                                <p><?php echo e(__('messages.Categories')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('products.index')); ?>" class="nav-link <?php echo e(request()->routeIs('products.*') ? 'active' : ''); ?>">
                                <i class="fas fa-box-open nav-icon"></i>
                                <p><?php echo e(__('messages.Products')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('deliveries.index')); ?>" class="nav-link <?php echo e(request()->routeIs('deliveries.*') ? 'active' : ''); ?>">
                                <i class="fas fa-shipping-fast nav-icon"></i>
                                <p><?php echo e(__('messages.Deliveries')); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Provider Management -->
                <li class="nav-item <?php echo e(request()->is('types*') || request()->is('provider-categories*') || request()->is('providers*') ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link <?php echo e(request()->is('types*') || request()->is('provider-categories*') || request()->is('providers*') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-handshake"></i>
                        <p>
                            <?php echo e(__('messages.Provider Management')); ?>

                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('types.index')); ?>" class="nav-link <?php echo e(request()->routeIs('types.*') ? 'active' : ''); ?>">
                                <i class="fas fa-list-ul nav-icon"></i>
                                <p><?php echo e(__('messages.types')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('provider-categories.index')); ?>" class="nav-link <?php echo e(request()->routeIs('provider-categories.*') ? 'active' : ''); ?>">
                                <i class="fas fa-layer-group nav-icon"></i>
                                <p><?php echo e(__('messages.provider-categories')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('providers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('providers.*') ? 'active' : ''); ?>">
                                <i class="fas fa-building nav-icon"></i>
                                <p><?php echo e(__('messages.providers')); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['order-table', 'order-add', 'order-edit', 'order-delete'])): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('orders.index')); ?>" class="nav-link <?php echo e(request()->routeIs('orders.*') ? 'active' : ''); ?>">
                            <i class="fas fa-shopping-cart nav-icon"></i>
                            <p><?php echo e(__('messages.orders')); ?></p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Financial Management -->
                <li class="nav-header"><?php echo e(__('messages.financial_management')); ?></li>

                <?php if($user->can('pos-table') || $user->can('pos-add') || $user->can('pos-edit') || $user->can('pos-delete')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('pos.index')); ?>" class="nav-link <?php echo e(request()->routeIs('pos.*') ? 'active' : ''); ?>">
                            <i class="fas fa-cash-register nav-icon"></i>
                            <p><?php echo e(__('messages.pos_list')); ?></p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if($user->can('card-table') || $user->can('card-add') || $user->can('card-edit') || $user->can('card-delete')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('cards.index')); ?>" class="nav-link <?php echo e(request()->routeIs('cards.*') ? 'active' : ''); ?>">
                            <i class="fas fa-credit-card nav-icon"></i>
                            <p><?php echo e(__('messages.cards_list')); ?></p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Content Management -->
                <li class="nav-header"><?php echo e(__('messages.content_management')); ?></li>

                <li class="nav-item">
                    <a href="<?php echo e(route('pages.index')); ?>" class="nav-link <?php echo e(request()->routeIs('pages.*') ? 'active' : ''); ?>">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <p><?php echo e(__('messages.pages_management')); ?></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('news.index')); ?>" class="nav-link <?php echo e(request()->routeIs('news.*') ? 'active' : ''); ?>">
                        <i class="fas fa-newspaper nav-icon"></i>
                        <p><?php echo e(__('messages.news')); ?></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('careers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('careers.*') ? 'active' : ''); ?>">
                        <i class="fas fa-briefcase nav-icon"></i>
                        <p><?php echo e(__('messages.careers')); ?></p>
                    </a>
                </li>

                <!-- System Settings -->
                <li class="nav-header"><?php echo e(__('messages.system_settings')); ?></li>

                <li class="nav-item">
                    <a href="<?php echo e(route('settings.index')); ?>" class="nav-link <?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>">
                        <i class="fas fa-cogs nav-icon"></i>
                        <p><?php echo e(__('messages.Settings')); ?></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('app-configs.index')); ?>" class="nav-link <?php echo e(request()->routeIs('app-configs.*') ? 'active' : ''); ?>">
                        <i class="fas fa-mobile-alt nav-icon"></i>
                        <p><?php echo e(__('messages.app_configurations')); ?></p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('notifications.create')); ?>" class="nav-link <?php echo e(request()->routeIs('notifications.*') ? 'active' : ''); ?>">
                        <i class="fas fa-bell nav-icon"></i>
                        <p><?php echo e(__('messages.notifications')); ?></p>
                    </a>
                </li>

                <!-- Admin Management -->
                <li class="nav-header"><?php echo e(__('messages.admin_management')); ?></li>

                <li class="nav-item">
                    <a href="<?php echo e(route('admin.login.edit', auth()->user()->id)); ?>" class="nav-link <?php echo e(request()->routeIs('admin.login.edit') ? 'active' : ''); ?>">
                        <i class="fas fa-user-circle nav-icon"></i>
                        <p><?php echo e(__('messages.Admin_account')); ?></p>
                    </a>
                </li>

                <?php if($user->can('role-table') || $user->can('role-add') || $user->can('role-edit') || $user->can('role-delete')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.role.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.role.*') ? 'active' : ''); ?>">
                            <i class="fas fa-shield-alt nav-icon"></i>
                            <p><?php echo e(__('messages.Roles')); ?></p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if($user->can('employee-table') || $user->can('employee-add') || $user->can('employee-edit') || $user->can('employee-delete')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.employee.index')); ?>" class="nav-link <?php echo e(request()->routeIs('admin.employee.*') ? 'active' : ''); ?>">
                            <i class="fas fa-user-tie nav-icon"></i>
                            <p><?php echo e(__('messages.Employee')); ?></p>
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/includes/sidebar.blade.php ENDPATH**/ ?>