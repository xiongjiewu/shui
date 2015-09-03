<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableActivityFundraising extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_fundraising', function ($table) {
            $table->integer('activity_id')->unique()->comment('活动ID');
            $table->integer('fundraising_count')->default(0)->comment('募捐人数');
            $table->decimal('total_amount_price')->default(0)->comment('需要募捐总额');
            $table->decimal('existing_price')->default(0)->comment('已捐总额');
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
        Schema::drop('activity_fundraising');
    }
}
