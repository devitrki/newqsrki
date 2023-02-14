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
        Schema::create('order_mode_pos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->smallInteger('order_mode_id');
            $table->string('order_mode_name');
            $table->string('sap_name');
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
        Schema::dropIfExists('order_mode_pos');
    }
};
