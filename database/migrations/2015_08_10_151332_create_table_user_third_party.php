<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserThirdParty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_third_party', function ($table) {
            $table->integer('user_id')->comment('用户自增ID');
            $table->string('user_other_id')->unique()->default('')->comment('第三方登入id');
            $table->tinyInteger('type')->default(1)->comment('1-微信2-QQ');
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
        Schema::drop('user_third_party');
    }
}
