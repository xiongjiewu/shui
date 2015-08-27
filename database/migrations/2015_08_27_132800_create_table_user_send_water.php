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
            $table->integer('user_id')->default('0')->comment('用户ID');
            $table->integer('water_count')->default('0')->comment('送出的亲水值');
            $table->integer('accept_user_id')->default('0')->comment('接受的用户ID');
            $table->timestamp('overdue_date')->comment('过期时间');
            $table->tinyInteger('status')->default('0')->comment('0-未领取 1-已领取');
            $table->tinyInteger('is_active')->default('1')->comment('1-有效 0无效');
            $table->timestamps();
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
