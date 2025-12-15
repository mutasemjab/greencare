<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->double('value');
            $table->timestamps();
        });

         DB::table('settings')->insert([
            ['key' => "amount_of_shower_patient", 'value' => 10],
            ['key' => "amount_of_transfer_patient_inside_amman", 'value' => 2],
            ['key' => "amount_of_transfer_patient_outside_amman", 'value' => 3],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
