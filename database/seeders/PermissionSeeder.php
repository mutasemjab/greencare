<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permissions_admin = [


            'role-table',
            'role-add',
            'role-edit',
            'role-delete',

            'employee-table',
            'employee-add',
            'employee-edit',
            'employee-delete',

            'user-table',
            'user-add',
            'user-edit',
            'user-delete',

            'doctor-table',
            'doctor-add',
            'doctor-edit',
            'doctor-delete',

            'nurse-table',
            'nurse-add',
            'nurse-edit',
            'nurse-delete',

            'delivery-table',
            'delivery-add',
            'delivery-edit',
            'delivery-delete',

            'notification-table',
            'notification-add',
            'notification-edit',
            'notification-delete',

            'setting-table',
            'setting-add',
            'setting-edit',
            'setting-delete',
       
            'page-table',
            'page-add',
            'page-edit',
            'page-delete',

            'report-template-table',
            'report-template-add',
            'report-template-edit',
            'report-template-delete',

            'room-table',
            'room-add',
            'room-edit',
            'room-delete',
            
            'type-table',
            'type-add',
            'type-edit',
            'type-delete',
            
            'providerCategory-table',
            'providerCategory-add',
            'providerCategory-edit',
            'providerCategory-delete',
        
            'provider-table',
            'provider-add',
            'provider-edit',
            'provider-delete',

            'category-table',
            'category-add',
            'category-edit',
            'category-delete',
            
            'brand-table',
            'brand-add',
            'brand-edit',
            'brand-delete',
        
            'product-table',
            'product-add',
            'product-edit',
            'product-delete',
        
            'order-table',
            'order-add',
            'order-edit',
            'order-delete',

        ];

         foreach ($permissions_admin as $permission_ad) {
            Permission::create(['name' => $permission_ad, 'guard_name' => 'admin']);
        }
    }
}
