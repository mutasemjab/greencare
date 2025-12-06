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

                <?php if($user->can('banner-table') || $user->can('banner-add') || $user->can('banner-edit') || $user->can('banner-delete')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('banners.index')); ?>" class="nav-link">
                            <i class="fas fa-images nav-icon"></i>
                            <p> <?php echo e(__('messages.Banners')); ?> </p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- User Management Section -->
                <li class="nav-item">
                    <a href="<?php echo e(route('users.index')); ?>" class="nav-link">
                        <i class="fas fa-users nav-icon"></i>
                        <p> <?php echo e(__('messages.users')); ?> </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('families.index')); ?>" class="nav-link">
                        <i class="fas fa-home nav-icon"></i>
                        <p> <?php echo e(__('messages.families')); ?> </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('doctors.index')); ?>" class="nav-link">
                        <i class="fas fa-user-md nav-icon"></i>
                        <p> <?php echo e(__('messages.doctors')); ?> </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('nurses.index')); ?>" class="nav-link">
                        <i class="fas fa-user-nurse nav-icon"></i>
                        <p> <?php echo e(__('messages.nurses')); ?> </p>
                    </a>
                </li>

                <!-- Medical Management -->
                <li class="nav-item">
                    <a href="<?php echo e(route('report-templates.index')); ?>" class="nav-link">
                        <i class="fas fa-file-medical nav-icon"></i>
                        <p> <?php echo e(__('messages.report_templates')); ?> </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('rooms.index')); ?>" class="nav-link">
                        <i class="fas fa-bed nav-icon"></i>
                        <p> <?php echo e(__('messages.rooms')); ?> </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('medications.index')); ?>" class="nav-link">
                        <i class="fas fa-pills nav-icon"></i>
                        <p> <?php echo e(__('messages.medications')); ?> </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('pledge-forms.index')); ?>" class="nav-link">
                        <i class="fas fa-file-signature nav-icon"></i>
                        <p> <?php echo e(__('messages.pledge_forms')); ?> </p>
                    </a>
                </li>

                <!-- Service Types Management -->
                <li class="nav-item <?php echo e(request()->is('elderly-cares*') || request()->is('request-nurses*') || request()->is('home-xrays*') || request()->is('medical-tests*') ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>
                            <?php echo e(__('messages.service_types_management')); ?>

                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('request-nurses.index')); ?>"
                                class="nav-link <?php echo e(request()->routeIs('request-nurses.*') ? 'active' : ''); ?>">
                                <i class="fas fa-heart nav-icon"></i>
                                <p><?php echo e(__('messages.request_nurses_types')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('elderly-cares.index')); ?>"
                                class="nav-link <?php echo e(request()->routeIs('elderly-cares.*') ? 'active' : ''); ?>">
                                <i class="fas fa-heart nav-icon"></i>
                                <p><?php echo e(__('messages.elderly_care_types')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('home-xrays.index')); ?>"
                                class="nav-link <?php echo e(request()->routeIs('home-xrays.*') ? 'active' : ''); ?>">
                                <i class="fas fa-x-ray nav-icon"></i>
                                <p><?php echo e(__('messages.home_xray_types')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('medical-tests.index')); ?>"
                                class="nav-link <?php echo e(request()->routeIs('medical-tests.*') ? 'active' : ''); ?>">
                                <i class="fas fa-flask nav-icon"></i>
                                <p><?php echo e(__('messages.medical_test_types')); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Appointments Management -->
                <li class="nav-item">
                    <a href="<?php echo e(route('appointments.index')); ?>" class="nav-link <?php echo e(request()->routeIs('appointments.*') ? 'active' : ''); ?>">
                        <i class="fas fa-calendar-check nav-icon"></i>
                        <p> <?php echo e(__('messages.all_appointments')); ?> </p>
                    </a>
                </li>
              
                <li class="nav-item">
                    <a href="<?php echo e(route('appointment-providers.index')); ?>" class="nav-link <?php echo e(request()->routeIs('appointment-providers.*') ? 'active' : ''); ?>">
                        <i class="fas fa-calendar-check nav-icon"></i>
                        <p> <?php echo e(__('messages.appointment_providers')); ?> </p>
                    </a>
                </li>

                <!-- Catalog Management (Existing Section) -->
                <li class="nav-item <?php echo e(request()->is('deliveries*') || request()->is('categories*') || request()->is('products*') ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>
                            <?php echo e(__('messages.Catalog_Management')); ?>

                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('brands.index')); ?>"
                                class="nav-link <?php echo e(request()->routeIs('brands.*') ? 'active' : ''); ?>">
                                <i class="fas fa-list nav-icon"></i>
                                <p><?php echo e(__('messages.Brands')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('categories.index')); ?>"
                                class="nav-link <?php echo e(request()->routeIs('categories.*') ? 'active' : ''); ?>">
                                <i class="fas fa-folder nav-icon"></i>
                                <p><?php echo e(__('messages.Categories')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('products.index')); ?>"
                                class="nav-link <?php echo e(request()->routeIs('products.*') ? 'active' : ''); ?>">
                                <i class="fas fa-box nav-icon"></i>
                                <p><?php echo e(__('messages.Products')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('deliveries.index')); ?>"
                                class="nav-link <?php echo e(request()->routeIs('deliveries.*') ? 'active' : ''); ?>">
                                <i class="fas fa-truck nav-icon"></i>
                                <p><?php echo e(__('messages.Deliveries')); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Provider Management (Existing Section) -->
                <li class="nav-item <?php echo e(request()->is('types*') || request()->is('provider-categories*') || request()->is('providers*') ? 'menu-open' : ''); ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>
                            <?php echo e(__('messages.Provider Management')); ?>

                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('types.index')); ?>"
                                class="nav-link <?php echo e(request()->routeIs('types.*') ? 'active' : ''); ?>">
                                <i class="fas fa-list nav-icon"></i>
                                <p><?php echo e(__('messages.types')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('provider-categories.index')); ?>"
                                class="nav-link <?php echo e(request()->routeIs('provider-categories.*') ? 'active' : ''); ?>">
                                <i class="fas fa-folder nav-icon"></i>
                                <p><?php echo e(__('messages.provider-categories')); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('providers.index')); ?>"
                                class="nav-link <?php echo e(request()->routeIs('providers.*') ? 'active' : ''); ?>">
                                <i class="fas fa-box nav-icon"></i>
                                <p><?php echo e(__('messages.providers')); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['order-table', 'order-add', 'order-edit', 'order-delete'])): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('orders.index')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('orders.index') ? 'active' : ''); ?>">
                            <i class="fas fa-handshake nav-icon"></i>
                            <p><?php echo e(__('messages.orders')); ?></p>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Content Management -->
                <li class="nav-item">
                    <a href="<?php echo e(route('pages.index')); ?>" class="nav-link">
                        <i class="fas fa-file-alt nav-icon"></i>
                        <p> <?php echo e(__('messages.pages_management')); ?> </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('news.index')); ?>" class="nav-link">
                        <i class="fas fa-bullhorn nav-icon"></i>
                        <p> <?php echo e(__('messages.news')); ?> </p>
                    </a>
                </li>

                <!-- System Configuration -->
                <li class="nav-item">
                    <a href="<?php echo e(route('settings.index')); ?>" class="nav-link">
                        <i class="fas fa-cog nav-icon"></i>
                        <p><?php echo e(__('messages.Settings')); ?> </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('app-configs.index')); ?>"
                        class="nav-link <?php echo e(request()->routeIs('app-configs.index') ? 'active' : ''); ?>">
                        <i class="fas fa-wrench nav-icon"></i>
                        <p><?php echo e(__('messages.app_configurations')); ?></p>
                    </a>
                </li>
          
                <li class="nav-item">
                    <a href="<?php echo e(route('notifications.create')); ?>"
                        class="nav-link <?php echo e(request()->routeIs('notifications.create') ? 'active' : ''); ?>">
                        <i class="fas fa-wrench nav-icon"></i>
                        <p><?php echo e(__('messages.notifications')); ?></p>
                    </a>
                </li>

                <!-- User Management -->
                <li class="nav-header"><?php echo e(__('messages.user_management')); ?></li>

                <li class="nav-item">
                    <a href="<?php echo e(route('admin.login.edit', auth()->user()->id)); ?>" class="nav-link">
                        <i class="fas fa-user nav-icon"></i>
                        <p><?php echo e(__('messages.Admin_account')); ?> </p>
                    </a>
                </li>

                <?php if($user->can('role-table') || $user->can('role-add') || $user->can('role-edit') || $user->can('role-delete')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.role.index')); ?>" class="nav-link">
                            <i class="fas fa-user-shield nav-icon"></i>
                            <span><?php echo e(__('messages.Roles')); ?> </span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(
                    $user->can('employee-table') ||
                        $user->can('employee-add') ||
                        $user->can('employee-edit') ||
                        $user->can('employee-delete')): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.employee.index')); ?>" class="nav-link">
                            <i class="fas fa-users nav-icon"></i>
                            <span> <?php echo e(__('messages.Employee')); ?> </span>
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside><?php /**PATH C:\xampp\htdocs\greencare\resources\views/admin/includes/sidebar.blade.php ENDPATH**/ ?>