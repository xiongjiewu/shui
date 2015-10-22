<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_support', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('user_id')->default('0')->comment('用户ID');
            $table->string('activity_id')->default('0')->comment('文章ID');
            $table->timestamps();
            $table->index(['user_id', 'activity_id'], 'u_a');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_support');
    }
}
