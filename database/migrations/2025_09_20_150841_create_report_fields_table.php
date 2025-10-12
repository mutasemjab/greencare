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
        Schema::create('report_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_section_id')->constrained()->onDelete('cascade');
            $table->string('label_en'); // e.g. "Blood Pressure", "Gender"
            $table->string('label_ar'); // e.g. "Blood Pressure", "Gender"
            $table->enum('input_type', ['text', 'textarea', 'number', 'date', 'select', 'radio', 'checkbox', 'boolean', 'gender','photo','pdf','signuture','medicine']);
            $table->boolean('required')->default(false);
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
        Schema::dropIfExists('report_fields');
    }
};
