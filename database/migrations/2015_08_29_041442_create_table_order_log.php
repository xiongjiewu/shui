<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrderLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_log', function ($table) {
            $table->increments('order_id')->comment('订单ID');
            $table->integer('user_id')->default('0')->comment('用户ID');
            $table->string('price')->default('0')->comment('金额');
            $table->string('water_count')->default('0')->comment('亲水值');
            $table->string('rate')->defalut('1')->comment('比率1:1(1块钱等于1亲水值)');
            $table->tinyInteger('status')->default('0')->comment('0-充值失败 1-充值成功');
            $table->tinyInteger('type')->default('1')->comment('1-充值 2-提现');
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
        Schema::drop('order_log');
    }
}
