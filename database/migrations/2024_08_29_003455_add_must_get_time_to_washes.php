<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMustGetTimeToWashes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('washes', function (Blueprint $table) {
            $table->string('exit_date')->nullable();
            $table->string('exit_time')->nullable();

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
