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
        Schema::create('lb_mon_sls_cas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_mon_sls_id')->index();
            $table->string('shift', 20)->index();
            $table->double('total_sales', 18, 2)->nullable();
            $table->double('total_non_cash', 18, 2)->nullable();
            $table->double('total_cash', 18, 2)->nullable();
            $table->double('brankas_money', 18, 2)->nullable();
            $table->double('pending_pc', 18, 2)->nullable();
            $table->string('hand_over_by')->nullable();
            $table->string('received_by')->nullable();
            $table->integer('p100')->nullable();
            $table->integer('p200')->nullable();
            $table->integer('p500')->nullable();
            $table->integer('p1000')->nullable();
            $table->integer('p2000')->nullable();
            $table->integer('p5000')->nullable();
            $table->integer('p10000')->nullable();
            $table->integer('p20000')->nullable();
            $table->integer('p50000')->nullable();
            $table->integer('p100000')->nullable();
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
        Schema::dropIfExists('lb_mon_sls_cas');
    }
};
