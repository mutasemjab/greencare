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
        Schema::create('medical_tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_medical_test_id');
            $table->foreign('type_medical_test_id')->references('id')->on('type_medical_tests')->onDelete('cascade');
            $table->date('date_of_appointment');
            $table->time('time_of_appointment')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('medical_tests');
    }
};
