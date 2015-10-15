<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserSendMsg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_verify', function ($table) {
            $table->increments('id')->comment('自增ID');
            $table->string('cellphone', 11)->default('')->comment('手机号');
            $table->string('verify')->default('')->comment('验证码');
            $table->string('expired_at')->default('')->comment('过期时间');
            $table->tinyInteger('status')->default('0')->comment('0-未使用 1-使用');
            $table->timestamps();
            $table->index(['cellphone', 'verify'], 'c_v');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_verify');
    }
}
