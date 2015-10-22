<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSystemHasWater extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_has_water', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('business_id')->default('0')->comment('来自的商家ID');
            $table->string('sys_water_count')->default('0')->comment('每次获得的公益值');
            $table->string('rate')->default(0)->comment('比率');
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
        Schema::drop('system_has_water');
    }
}
