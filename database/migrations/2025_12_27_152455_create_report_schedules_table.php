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
        Schema::create('report_schedules', function (Blueprint $table) {
            $table->id();
             $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('report_template_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // الممرض/الدكتور المستهدف
            $table->dateTime('scheduled_for'); // موعد الإشعار
            $table->boolean('notification_sent')->default(false);
            $table->timestamp('notification_sent_at')->nullable();
            $table->boolean('completed')->default(false); // هل تم تعبئة التقرير
            $table->foreignId('report_id')->nullable()->constrained()->onDelete('set null'); // التقرير المُعبأ
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
        Schema::dropIfExists('report_schedules');
    }
};
