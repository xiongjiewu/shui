<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableActivityImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_image', function ($table) {
            $table->integer('activity_id')->comment('活动ID');
            $table->string('image_url')->default('')->comment('图片地址');
            $table->tinyInteger('type')->default('1')->comment('1-静态图片 2-动态视频');
            $table->tinyInteger('is_completion')->default('0')->comment('0相对路径 1-绝对路径');
            $table->timestamps();
            $table->index('activity_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('activity_image');
    }
}
