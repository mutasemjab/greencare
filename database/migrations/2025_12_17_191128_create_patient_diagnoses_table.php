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
        Schema::create('patient_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_provider_id')->constrained('appointment_providers')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('diagnosed_by')->constrained('users')->onDelete('cascade'); // nurse/doctor who wrote diagnosis
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('diagnosis'); // التشخيص
            $table->text('symptoms')->nullable(); // الأعراض
            $table->text('treatment_plan')->nullable(); // خطة العلاج
            $table->text('notes')->nullable(); // ملاحظات
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
        Schema::dropIfExists('patient_diagnoses');
    }
};
