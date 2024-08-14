<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->default('');
            $table->string('social_id')->default('');
            $table->string('group_id')->nullable();
            $table->string('group_name')->nullable();
            $table->string('group_type')->nullable();
            $table->dateTime('date');
            $table->text('text');
            $table->string('message_type')->default('');
            $table->string('keyword')->nullable();
            $table->string('value')->nullable();
            $table->string('reply_token')->default('');
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
        Schema::dropIfExists('messages');
    }
}
