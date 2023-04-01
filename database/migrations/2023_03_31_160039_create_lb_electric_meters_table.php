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
        Schema::create('lb_electric_meters', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_app_review_id')->index();
            $table->string('month', 2);
            $table->string('year', 4);
            $table->string('initial_meter', 20);
            $table->string('final_meter', 20);
            $table->string('usage', 20);
            $table->string('pic');
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
        Schema::dropIfExists('lb_electric_meters');
    }
};
