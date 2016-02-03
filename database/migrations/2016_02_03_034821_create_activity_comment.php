<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_comment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id')->comment('活动ID');
            $table->integer('user_id')->comment('用户ID');
            $table->string('content')->comment('内容');
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
        Schema::drop('user_rank');
    }
}
