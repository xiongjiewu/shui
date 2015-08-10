<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_image', function ($table) {
            $table->integer('user_id')->comment('用户自增ID');
            $table->string('image_url')->default('')->comment('图片路径');
            $table->tinyInteger('type')->default(1)->comment('1-头像2-营业执照3-店铺招牌');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_image');
    }
}
