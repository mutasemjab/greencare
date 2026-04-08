<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE report_fields MODIFY COLUMN input_type ENUM('text','textarea','number','date','select','radio','checkbox','gender','photo','pdf','signuture','medicine') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE report_fields MODIFY COLUMN input_type ENUM('text','textarea','number','date','select','radio','checkbox','boolean','gender','photo','pdf','signuture','medicine') NOT NULL");
    }
};
