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
        Schema::create('medication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_id')->constrained()->onDelete('cascade');
            $table->dateTime('scheduled_time');
            $table->boolean('taken')->default(false); // 0 = ما أخذ، 1 = أخذ
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
        Schema::dropIfExists('medication_logs');
    }
};
