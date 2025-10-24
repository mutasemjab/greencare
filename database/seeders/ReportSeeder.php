<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        /*
        |--------------------------------------------------------------------------
        | 1. Initial Report Template (for new patient room)
        |--------------------------------------------------------------------------
        */
        $initialTemplateId = DB::table('report_templates')->insertGetId([
            'title_en' => 'Initial Patient Assessment',
            'title_ar' => 'التقييم الأولي للمريض',
            'created_for' => 'doctor',
            'frequency' => 'one_time',
            'report_type' => 'initial_setup',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Sections for initial report
        $section1 = DB::table('report_sections')->insertGetId([
            'report_template_id' => $initialTemplateId,
            'title_en' => 'Basic Information',
            'title_ar' => 'المعلومات الأساسية',
            'order' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $section2 = DB::table('report_sections')->insertGetId([
            'report_template_id' => $initialTemplateId,
            'title_en' => 'Initial Diagnosis',
            'title_ar' => 'التشخيص الأولي',
            'order' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Fields for section 1
        DB::table('report_fields')->insert([
            [
                'report_section_id' => $section1,
                'label_en' => 'Patient Name',
                'label_ar' => 'اسم المريض',
                'input_type' => 'text',
                'required' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'report_section_id' => $section1,
                'label_en' => 'Gender',
                'label_ar' => 'الجنس',
                'input_type' => 'gender',
                'required' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'report_section_id' => $section1,
                'label_en' => 'Date of Birth',
                'label_ar' => 'تاريخ الميلاد',
                'input_type' => 'date',
                'required' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Fields for section 2
        DB::table('report_fields')->insert([
            [
                'report_section_id' => $section2,
                'label_en' => 'Symptoms Description',
                'label_ar' => 'وصف الأعراض',
                'input_type' => 'textarea',
                'required' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'report_section_id' => $section2,
                'label_en' => 'Initial Diagnosis Summary',
                'label_ar' => 'ملخص التشخيص الأولي',
                'input_type' => 'textarea',
                'required' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. Nurse Hourly Report Template
        |--------------------------------------------------------------------------
        */
        $nurseTemplateId = DB::table('report_templates')->insertGetId([
            'title_en' => 'Nurse Hourly Report',
            'title_ar' => 'تقرير الممرضة الساعي',
            'created_for' => 'nurse',
            'frequency' => 'daily',
            'report_type' => 'recurring',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $nurseSection1 = DB::table('report_sections')->insertGetId([
            'report_template_id' => $nurseTemplateId,
            'title_en' => 'Vital Signs',
            'title_ar' => 'العلامات الحيوية',
            'order' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $nurseSection2 = DB::table('report_sections')->insertGetId([
            'report_template_id' => $nurseTemplateId,
            'title_en' => 'Patient Care Notes',
            'title_ar' => 'ملاحظات العناية بالمريض',
            'order' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Fields for nurse report
        $bpFieldId = DB::table('report_fields')->insertGetId([
            'report_section_id' => $nurseSection1,
            'label_en' => 'Blood Pressure',
            'label_ar' => 'ضغط الدم',
            'input_type' => 'text',
            'required' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $pulseFieldId = DB::table('report_fields')->insertGetId([
            'report_section_id' => $nurseSection1,
            'label_en' => 'Pulse Rate',
            'label_ar' => 'معدل النبض',
            'input_type' => 'number',
            'required' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $tempFieldId = DB::table('report_fields')->insertGetId([
            'report_section_id' => $nurseSection1,
            'label_en' => 'Temperature',
            'label_ar' => 'درجة الحرارة',
            'input_type' => 'number',
            'required' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('report_fields')->insert([
            'report_section_id' => $nurseSection2,
            'label_en' => 'General Notes',
            'label_ar' => 'ملاحظات عامة',
            'input_type' => 'textarea',
            'required' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. Doctor Monthly Report Template
        |--------------------------------------------------------------------------
        */
        $doctorTemplateId = DB::table('report_templates')->insertGetId([
            'title_en' => 'Doctor Monthly Report',
            'title_ar' => 'تقرير الطبيب الشهري',
            'created_for' => 'doctor',
            'frequency' => 'monthly',
            'report_type' => 'recurring',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $doctorSection = DB::table('report_sections')->insertGetId([
            'report_template_id' => $doctorTemplateId,
            'title_en' => 'Monthly Evaluation',
            'title_ar' => 'التقييم الشهري',
            'order' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $field1 = DB::table('report_fields')->insertGetId([
            'report_section_id' => $doctorSection,
            'label_en' => 'General Condition',
            'label_ar' => 'الحالة العامة',
            'input_type' => 'select',
            'required' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Field options for condition
        DB::table('report_field_options')->insert([
            [
                'report_field_id' => $field1,
                'value_en' => 'Stable',
                'value_ar' => 'مستقرة',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'report_field_id' => $field1,
                'value_en' => 'Improving',
                'value_ar' => 'في تحسن',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'report_field_id' => $field1,
                'value_en' => 'Critical',
                'value_ar' => 'حرجة',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('report_fields')->insert([
            'report_section_id' => $doctorSection,
            'label_en' => 'Doctor Notes',
            'label_ar' => 'ملاحظات الطبيب',
            'input_type' => 'textarea',
            'required' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
