<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAppReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report', function ($table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('user_id')->comment('用户ID');
            $table->string('report')->default('')->comment('反馈');
            $table->tinyInteger('type')->default('1')->comment('1-用户反馈 2-商户反馈');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('report');
    }
}
