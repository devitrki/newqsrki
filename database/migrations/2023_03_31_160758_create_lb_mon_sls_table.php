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
        Schema::create('lb_mon_sls', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_app_review_id')->index();
            $table->string('name')->nullable();
            $table->string('nik')->nullable();
            $table->string('function')->nullable();
            $table->string('total_money')->nullable();
            $table->date('deposit_date')->nullable();
            $table->string('deposit_to')->nullable();
            $table->double('dp_ulang_tahun', 18, 2)->nullable();
            $table->double('dp_big_order', 18, 2)->nullable();
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
        Schema::dropIfExists('lb_mon_sls');
    }
};
