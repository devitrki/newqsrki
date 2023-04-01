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
        Schema::create('lb_prod_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_app_review_id')->index();
            $table->string('product')->index();
            $table->string('time_check_quality', 50)->nullable();
            $table->double('total_usage', 18, 2)->nullable();
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
        Schema::dropIfExists('lb_prod_plans');
    }
};
