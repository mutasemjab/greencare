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
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title_en'); // e.g. "Initial Diagnosis", "Follow-up Report"
            $table->string('title_ar'); // e.g. "Initial Diagnosis", "Follow-up Report"
            $table->enum('created_for', ['doctor', 'nurse']); // who uses this template
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_templates');
    }
};
