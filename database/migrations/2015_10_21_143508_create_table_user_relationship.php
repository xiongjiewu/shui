<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_relationship', function ($table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('user_id')->default('0')->comment('用户ID');
            $table->integer('guest_id')->default('0')->comment('被邀请的人ID');
            $table->timestamps();
            $table->index(['guest_id', 'user_id'], 'g_u');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_relationship');
    }
}
