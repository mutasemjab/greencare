<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            // Drop old FK with CASCADE (deletes reports when user is deleted)
            $table->dropForeign(['created_by']);
            // Re-add with SET NULL so deleting a user doesn't wipe their reports
            $table->foreignId('created_by')->nullable()->change();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('report_answers', function (Blueprint $table) {
            // Drop old FK with CASCADE (deletes answers when a template field is deleted)
            $table->dropForeign(['report_field_id']);
            // Re-add with SET NULL so removing a field doesn't wipe historical answers
            $table->foreignId('report_field_id')->nullable()->change();
            $table->foreign('report_field_id')->references('id')->on('report_fields')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->foreignId('created_by')->nullable(false)->change();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('report_answers', function (Blueprint $table) {
            $table->dropForeign(['report_field_id']);
            $table->foreignId('report_field_id')->nullable(false)->change();
            $table->foreign('report_field_id')->references('id')->on('report_fields')->onDelete('cascade');
        });
    }
};
