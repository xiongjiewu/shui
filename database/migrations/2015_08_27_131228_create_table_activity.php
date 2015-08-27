<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity', function ($table) {
            $table->increments('activity_id')->comment('活动ID');
            $table->string('title')->default('')->comment('活动标题');
            $table->string('desc')->default('')->comment('活动描述');
            $table->string('statement')->default('')->comment('活动声明');
            $table->string('url')->default('')->comment('活动URL');
            $table->integer('focus_count')->default('0')->comment('支持数');
            $table->tinyInteger('status')->default('1')->comment('1-正常 2-关闭');
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
        Schema::drop('activity');
    }
}
