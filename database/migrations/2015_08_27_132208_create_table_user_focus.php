<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserFocus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_focus', function ($table) {
            $table->integer('activity_id')->comment('活动ID');
            $table->integer('user_id')->comment('用户ID');
            $table->tinyInteger('is_active')->default('1')->comment('1-有效0-无效');
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
        Schema::drop('user_focus');
    }
}
