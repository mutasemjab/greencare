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
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'order', 'appointment', 'appointment_provider'
            $table->string('title');
            $table->text('message');
            $table->unsignedBigInteger('related_id')->nullable(); // ID of order/appointment
            $table->string('url')->nullable(); // Link to view details
            $table->json('data')->nullable(); // Extra data
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['is_read', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_notifications');
    }
};
