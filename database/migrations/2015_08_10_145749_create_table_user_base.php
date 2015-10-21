<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserBase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_base', function ($table) {
            $table->increments('user_id')->comment('用户自增ID');
            $table->string('user_cellphone', 11)->unique()->default('')->comment('手机号');
            $table->string('password')->default('')->comment('密码');
            $table->string('user_name')->default('')->comment('用户名或者商户名');
            $table->tinyInteger('type')->default(1)->comment('1-用户2-商户3-管理员');
            $table->tinyInteger('status')->default(1)->comment('1-正常2-关闭');
            $table->string('invite_code')->comment('自动生成用户唯一邀请码');
            $table->timestamps();
            $table->index('invite_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_base');
    }
}
