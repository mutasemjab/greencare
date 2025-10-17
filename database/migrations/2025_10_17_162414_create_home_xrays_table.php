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
        Schema::create('home_xrays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_home_xray_id');
            $table->foreign('type_home_xray_id')->references('id')->on('type_home_xrays')->onDelete('cascade');
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
        Schema::dropIfExists('home_xrays');
    }
};
