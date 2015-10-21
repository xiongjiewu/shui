<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserShareLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_share_log', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('user_id')->default('0')->comment('分享用户ID');
            $table->integer('share_water_count')->default('0')->comment('分享的亲水总值');
            $table->string('share_time')->default('0')->comment('分享的批次');
            $table->integer('share_count')->default('0')->comment('分享的次数');
            $table->integer('share_receive')->default('0')->comment('已经被领取的亲水值');
            $table->tinyInteger('status')->default('0')->comment('1-可以领取 0-禁止领取');
            $table->timestamps();
            $table->index('share_time');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_share_log');
    }
}
