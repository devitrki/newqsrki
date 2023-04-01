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
        Schema::create('lb_duty_rosters', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_briefing_id')->index();
            $table->string('shift', 50)->nullable();
            $table->string('mod', 150)->nullable();
            $table->string('cashier', 150)->nullable();
            $table->string('kitchen', 150)->nullable();
            $table->string('lobby', 150)->nullable();
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
        Schema::dropIfExists('lb_duty_rosters');
    }
};
