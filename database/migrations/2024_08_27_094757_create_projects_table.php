<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->default('')->comment('名稱');
            $table->string('description')->default('')->comment('描述');
            $table->decimal('use_time')->nullable()->comment('需時(hr)');
            $table->string('price')->nullable()->comment('含三種車型');
            $table->string('discount_price')->nullable()->comment('含三種車型');
            $table->tinyInteger('status')->default(1)->comment('狀態');
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
        Schema::dropIfExists('projects');
    }
}
