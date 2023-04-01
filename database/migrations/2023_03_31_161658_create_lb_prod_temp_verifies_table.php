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
        Schema::create('lb_prod_temp_verifies', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_prod_plan_id')->index();
            $table->string('fryer', 5)->nullable();
            $table->string('shift1_temp', 50)->nullable();
            $table->string('shift2_temp', 50)->nullable();
            $table->string('shift3_temp', 50)->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('lb_prod_temp_verifies');
    }
};
