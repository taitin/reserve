<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToAdditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('additions', function (Blueprint $table) {
            $table->tinyInteger('use_discount')->nullable()->comment('使用折扣')->default(1);
            $table->date('addition_start')->nullable()->comment('方案開始時間');
            $table->date('addition_end')->nullable()->comment('方案結束時間');
            $table->integer('order')->nullable()->comment('排序')->default(0);
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
        Schema::table('additions', function (Blueprint $table) {
            //
        });
    }
}
