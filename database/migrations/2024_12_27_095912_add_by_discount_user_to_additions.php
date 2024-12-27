<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddByDiscountUserToAdditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('additions', function (Blueprint $table) {
            $table->tinyInteger('by_discount_user')->default(0)->comment('可使用優惠的使用者');
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
