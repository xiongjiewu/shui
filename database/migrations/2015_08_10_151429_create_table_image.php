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
            $table->increments('id')->comment('图片自增ID');
            $table->integer('user_id')->comment('用户自增ID');
            $table->string('image_url')->default('')->comment('图片路径');
            $table->tinyInteger('type')->default(1)->comment('1-用户头像(LOGO)2-营业执照3-店铺实景');
            $table->tinyInteger('is_completion')->default(0)->comment('0-系统路径1-微信路径2-七牛路径');
            $table->index(['user_id'], 'user_index');
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
        Schema::drop('user_image');
    }
}
