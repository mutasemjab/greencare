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
        Schema::create('room_report_template_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('report_template_id')->constrained()->onDelete('cascade');
            $table->dateTime('assigned_at'); // When template was assigned
            $table->dateTime('replaced_at')->nullable(); // When it was replaced with another
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade'); // Who assigned it
            $table->boolean('is_active')->default(true); // Current active template
            $table->text('notes')->nullable(); // Optional notes about why changed
            $table->timestamps();
            
            // Index for performance
            $table->index(['room_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_report_template_histories');
    }
};
