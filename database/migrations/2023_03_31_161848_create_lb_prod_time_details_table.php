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
        Schema::create('lb_prod_time_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_prod_time_id')->index();
            $table->double('quantity', 18, 2)->nullable();
            $table->string('exp_prod_code')->nullable();
            $table->string('fryer', 5)->nullable();
            $table->string('temperature', 50)->nullable();
            $table->string('holding_time')->nullable();
            $table->string('self_life')->nullable();
            $table->string('vendor')->nullable();
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
        Schema::dropIfExists('lb_prod_time_details');
    }
};
