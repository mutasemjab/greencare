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
        // نقل مرضى
        Schema::create('transfer_patients', function (Blueprint $table) {
            $table->id();
            $table->date('date_of_transfer');
            $table->time('time_of_transfer')->nullable();
            $table->text('note')->nullable();
            $table->string('from_address')->nullable(); 
            $table->double('from_lat')->nullable();
            $table->double('from_lng')->nullable();
            $table->tinyInteger('from_place')->default(1); // 1 inside amman, 2 outside amman
            $table->string('to_address')->nullable(); 
            $table->double('to_lat')->nullable();
            $table->double('to_lng')->nullable();
            $table->tinyInteger('to_place')->default(1); // 1 inside amman, 2 outside amman
            $table->unsignedBigInteger('user_id')->nullable();
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
        Schema::dropIfExists('transfer_patients');
    }
};
