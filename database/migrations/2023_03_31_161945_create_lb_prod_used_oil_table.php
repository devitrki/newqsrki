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
        Schema::create('lb_prod_used_oil', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_prod_plan_id')->index();
            $table->double('stock_first', 18, 2)->nullable();
            $table->double('stock_in_gr', 18, 2)->nullable();
            $table->double('stock_in_fryer_a', 18, 2)->nullable();
            $table->double('stock_in_fryer_b', 18, 2)->nullable();
            $table->double('stock_in_fryer_c', 18, 2)->nullable();
            $table->double('stock_in_fryer_d', 18, 2)->nullable();
            $table->double('stock_change_oil', 18, 2)->nullable();
            $table->double('stock_out', 18, 2)->nullable();
            $table->double('stock_last', 18, 2)->nullable();
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
        Schema::dropIfExists('lb_prod_used_oil');
    }
};
