<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIpToAutopassMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('autpoass_members', function (Blueprint $table) {
            $table->string('ip')->nullable()->comment('IP地址');
            $table->string('agent')->nullable()->after('ip')->comment('設備');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('autopass_members', function (Blueprint $table) {
            //
        });
    }
}
