<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserExtend extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_company_extend', function ($table) {
            $table->integer('user_id')->comment('用户自增ID');
            $table->string('user_address')->default('')->comment('用户地址');
            $table->string('user_company_name')->default('')->comment('用户公司名字');
            $table->string('user_desc')->default('')->comment('用户介绍');
            $table->string('user_http')->default('')->comment('用户公司网站');
            $table->string('user_company_lat')->default('')->comment('公司经度');
            $table->string('user_company_lng')->default('')->comment('公司纬度');
            $table->index(['user_id'], 'user_index');
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_company_extend');
    }
}
