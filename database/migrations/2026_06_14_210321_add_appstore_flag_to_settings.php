<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('settings')->insertOrIgnore([
            ['key' => 'appstore_flag', 'value' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        DB::table('settings')->where('key', 'appstore_flag')->delete();
    }
};
