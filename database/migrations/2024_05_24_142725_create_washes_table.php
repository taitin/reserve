<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('washes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('social_id')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('license')->nullable();
            $table->string('model')->nullable();
            $table->string('parking')->nullable();
            $table->dateTime('entry_time')->nullable();
            $table->dateTime('exit_time')->nullable();
            $table->string('status')->default('');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('washes');
    }
}
