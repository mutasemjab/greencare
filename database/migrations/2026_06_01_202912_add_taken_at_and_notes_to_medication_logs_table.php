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
        Schema::table('medication_logs', function (Blueprint $table) {
            $table->timestamp('taken_at')->nullable()->after('taken');
            $table->text('notes')->nullable()->after('taken_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medication_logs', function (Blueprint $table) {
            $table->dropColumn(['taken_at', 'notes']);
        });
    }
};
