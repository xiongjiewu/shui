<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserShareReceiveLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_share_receive_log', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('share_id')->default('0')->comment('分享的自增ID');
            $table->integer('share_receive_user_id')->default('0')->comment('分享领取的ID');
            $table->string('share_water_count')->default('0')->comment('领取的亲水值');
            $table->timestamps();
            $table->index(['share_id', 'share_receive_user_id'], 's_s');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_share_receive_log');
    }
}
