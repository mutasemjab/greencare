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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade'); 
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('cascade'); 
            $table->string('name'); // اسم الدواء
            $table->string('dosage')->nullable(); // مثلاً 500mg
            $table->integer('quantity')->nullable(); // الكمية في كل مرة
            $table->text('notes')->nullable(); // ملاحظات إضافية من الطبيب
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
        Schema::dropIfExists('medications');
    }
};
