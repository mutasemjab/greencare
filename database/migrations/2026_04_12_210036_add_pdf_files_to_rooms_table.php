<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('contract_pdf')->nullable()->after('code');
            $table->string('pledge_form_pdf')->nullable()->after('contract_pdf');
            $table->string('authorization_form_pdf')->nullable()->after('pledge_form_pdf');
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['contract_pdf', 'pledge_form_pdf', 'authorization_form_pdf']);
        });
    }
};
