<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserFinancial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_financial', function ($table) {
            $table->integer('user_id')->unique()->comment('用户自增ID');
            $table->decimal('water_count')->comment('亲水值');
            $table->decimal('price')->comment('用户充值总金额');
            $table->decimal('send_water')->default('0')->comment('已发亲水量');
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
        Schema::drop('user_financial');
    }
}
