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
            $table->text('address')->nullable();
            $table->double('lat')->nullable();
            $table->double('lng')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('room_id')->nullable();
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->enum('status', ['pending', 'confirmed', 'processing', 'finished', 'cancelled'])
                ->default('pending');
            $table->unsignedBigInteger('lab_id')->nullable();
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('set null');
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
