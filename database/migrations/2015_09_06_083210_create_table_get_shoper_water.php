<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGetShoperWater extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('get_shop_water_log', function ($table) {
            $table->integer('user_id')->unique()->default('0')->comment('商户ID');
            $table->decimal('water_count')->default('0')->comment('亲水值');
            $table->integer('giving_user_id')->unique()->default('0')->comment('领取的用户ID');
            $table->timestamps();
            $table->index(['user_id', 'giving_user_id'], 'u_g');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('get_shop_water_log');
    }
}
