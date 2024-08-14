<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToWashes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('washes', function (Blueprint $table) {
            //
            $table->decimal('price', 10, 0)->default(0)->comment('價格');
            $table->dateTime('arrive_at')->nullable()->comment('預計到達時間');
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
