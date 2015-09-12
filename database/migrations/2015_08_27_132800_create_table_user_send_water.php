<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserSendWater extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_send_water', function ($table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('user_id')->default('0')->comment('用户ID');
            $table->integer('water_count')->default('0')->comment('送出的亲水值');
            $table->integer('accept_user_id')->default('0')->comment('接受的用户ID');
            $table->integer('overdue_date')->comment('过期时间');
            $table->tinyInteger('status')->default('0')->comment('0-未领取 1-已领取 2-无效');
            $table->timestamps();
            $table->index(['user_id']);
            $table->index(['accept_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_send_water');
    }
}
