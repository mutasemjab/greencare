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
        Schema::create('request_nurses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_request_nurse_id');
            $table->foreign('type_request_nurse_id')->references('id')->on('type_request_nurses')->onDelete('cascade');
            $table->date('date_of_appointment');
            $table->time('time_of_appointment')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('room_id')->nullable();
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
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
        Schema::dropIfExists('request_nurses');
    }
};
