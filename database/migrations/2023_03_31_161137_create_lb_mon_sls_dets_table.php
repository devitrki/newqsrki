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
        Schema::create('lb_mon_sls_dets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_mon_sls_id')->index();
            $table->date('date')->nullable();
            $table->string('day')->nullable();
            $table->double('cash', 18, 2)->default('0');
            $table->double('total_non_cash', 18, 2)->default('0');
            $table->double('total_sales', 18, 2)->default('0');
            $table->string('hand_over_by')->nullable();
            $table->string('received_by')->nullable();
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
        Schema::dropIfExists('lb_mon_sls_dets');
    }
};
