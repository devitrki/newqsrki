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
        Schema::create('lb_temperatures', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_app_review_id')->index();
            $table->string('name');
            $table->integer('top_value');
            $table->integer('bottom_value');
            $table->integer('top_value_center');
            $table->integer('bottom_value_center');
            $table->integer('interval');
            $table->string('uom', 20)->nullable();
            $table->string('temp_1', 20)->nullable();
            $table->string('temp_2', 20)->nullable();
            $table->string('temp_3', 20)->nullable();
            $table->string('temp_4', 20)->nullable();
            $table->string('temp_5', 20)->nullable();
            $table->text('note');
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
        Schema::dropIfExists('lb_temperatures');
    }
};
