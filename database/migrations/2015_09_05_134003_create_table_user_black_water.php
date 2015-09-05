<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserBlackWater extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_black_water', function ($table) {
            $table->integer('user_id')->unique()->default('0')->comment('用户ID');
            $table->integer('black_water')->default('0')->comment('黑水值');
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
        Schema::drop('user_black_water');
    }
}
