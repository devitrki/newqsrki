<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lb_prod_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_prod_plan_id')->index();
            $table->string('time', 50)->nullable();
            $table->double('plan_cooking', 18, 2)->nullable();
            $table->double('plan_cooking_total', 18, 2)->nullable();
            $table->double('act_cooking', 18, 2)->nullable();
            $table->double('act_cooking_total', 18, 2)->nullable();
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
        Schema::dropIfExists('lb_prod_times');
    }
};
