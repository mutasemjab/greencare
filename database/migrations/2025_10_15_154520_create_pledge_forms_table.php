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
        Schema::create('pledge_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name_of_nurse');
            $table->string('name_of_patient');
            $table->string('identity_number_of_patient');
            $table->string('phone_of_patient')->nullable();
            $table->string('professional_license_number')->nullable();
            $table->text('pledge_text')->nullable();
            $table->date('date_of_pledge')->nullable();
            $table->string('signature_one')->nullable(); // photo
            $table->string('signature_two')->nullable();// photo
            $table->string('signature_three')->nullable();// photo
            $table->string('signature_four')->nullable();// photo
           
            $table->enum('type',['pledge_form','authorization_form'])->default('pledge_form');

            // for type authorization_form
             $table->string('place')->nullable();
             $table->date('date_of_birth')->nullable();
             $table->string('parent_of_patient')->nullable();
             $table->string('identity_number_for_parent_of_patient')->nullable();
             $table->string('phone_for_parent_of_patient')->nullable();
             $table->string('kinship')->nullable();
             $table->string('full_name_of_commissioner')->nullable();
             $table->unsignedBigInteger('room_id');
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
        Schema::dropIfExists('pledge_forms');
    }
};
