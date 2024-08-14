<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDateWashes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('washes', function (Blueprint $table) {
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->dropColumn('entry_time');
            $table->dropColumn('exit_time');
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
        Schema::table('washes', function (Blueprint $table) {
            //
        });
    }
}
