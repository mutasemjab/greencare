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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type', ['patient', 'nurse', 'doctor','super_nurse'])->default('patient');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->unique();
            $table->date('date_of_birth');
            $table->tinyInteger('gender')->default(1); // 1 man // 2 woman
            $table->tinyInteger('activate')->default(1); // 1 yes //2 no
            $table->string('photo')->nullable();
            $table->text('fcm_token')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
