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
        
            'appointmentProvider-table',
            'appointmentProvider-add',
            'appointmentProvider-edit',
            'appointmentProvider-delete',
        
            'lab-table',
            'lab-add',
            'lab-edit',
            'lab-delete',

            'adminNotification-table',
            'adminNotification-add',
            'adminNotification-edit',
            'adminNotification-delete',

            'appConfig-table',
            'appConfig-add',
            'appConfig-edit',
            'appConfig-delete',

            'appointment-table',
            'appointment-add',
            'appointment-edit',
            'appointment-delete',

            'banner-table',
            'banner-add',
            'banner-edit',
            'banner-delete',

            'card-table',
            'card-add',
            'card-edit',
            'card-delete',

            'cardNumber-table',
            'cardNumber-add',
            'cardNumber-edit',
            'cardNumber-delete',

            'career-table',
            'career-add',
            'career-edit',
            'career-delete',

            'coupon-table',
            'coupon-add',
            'coupon-edit',
            'coupon-delete',

            'family-table',
            'family-add',
            'family-edit',
            'family-delete',

            'news-table',
            'news-add',
            'news-edit',
            'news-delete',

            'pledgeForm-table',
            'pledgeForm-add',
            'pledgeForm-edit',
            'pledgeForm-delete',

            'pos-table',
            'pos-add',
            'pos-edit',
            'pos-delete',

            'shower-table',
            'shower-add',
            'shower-edit',
            'shower-delete',

            'specialMedicalForm-table',
            'specialMedicalForm-add',
            'specialMedicalForm-edit',
            'specialMedicalForm-delete',

            'transferPatient-table',
            'transferPatient-add',
            'transferPatient-edit',
            'transferPatient-delete',

            'typeElderlyCare-table',
            'typeElderlyCare-add',
            'typeElderlyCare-edit',
            'typeElderlyCare-delete',

            'typeHomeXray-table',
            'typeHomeXray-add',
            'typeHomeXray-edit',
            'typeHomeXray-delete',

            'typeMedicalTest-table',
            'typeMedicalTest-add',
            'typeMedicalTest-edit',
            'typeMedicalTest-delete',

            'typeRequestNurse-table',
            'typeRequestNurse-add',
            'typeRequestNurse-edit',
            'typeRequestNurse-delete',

            'dashboard-view',

            'fcm-send',

        ];

         foreach ($permissions_admin as $permission_ad) {
            Permission::create(['name' => $permission_ad, 'guard_name' => 'admin']);
        }
    }
}
