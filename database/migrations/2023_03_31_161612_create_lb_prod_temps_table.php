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
        Schema::create('lb_prod_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_prod_plan_id')->index();
            $table->string('food_name')->nullable();
            $table->string('time', 50)->nullable();
            $table->string('fryer_temp', 50)->nullable();
            $table->string('product_temp', 50)->nullable();
            $table->string('result', 50)->nullable();
            $table->string('corrective_action')->nullable();
            $table->string('pic')->nullable();
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
        Schema::dropIfExists('lb_prod_temps');
    }
};
