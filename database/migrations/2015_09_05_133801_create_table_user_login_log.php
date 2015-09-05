<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserLoginLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_login_log', function ($table) {
            $table->integer('user_id')->default('0')->comment('用户ID');
            $table->integer('date')->default('0')->comment('登入日期');
            $table->timestamps();
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_login_log');
    }
}
