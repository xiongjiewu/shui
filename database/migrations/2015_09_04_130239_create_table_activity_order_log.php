<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableActivityOrderLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_donations_log', function ($table) {
            $table->integer('active_id')->comment('活动ID');
            $table->integer('user_id')->default('0')->comment('用户ID');
            $table->string('water_count')->default('0')->comment('捐款亲水值');
            $table->string('price')->default('0')->comment('比率换算下来的捐款金额');
            $table->string('rate')->defalut('1')->comment('比率1:1(1块钱等于1亲水值)');
            $table->timestamps();
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('activity_donations_log');
    }
}
