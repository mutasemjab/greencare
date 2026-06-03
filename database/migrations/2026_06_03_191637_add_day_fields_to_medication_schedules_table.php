<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medication_schedules', function (Blueprint $table) {
            // 0=Sunday,1=Monday,...,6=Saturday — only for weekly
            $table->tinyInteger('day_of_week')->nullable()->after('frequency');
            // 1-31 — only for monthly
            $table->tinyInteger('day_of_month')->nullable()->after('day_of_week');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medication_schedules', function (Blueprint $table) {
            $table->dropColumn(['day_of_week', 'day_of_month']);
        });
    }
};
