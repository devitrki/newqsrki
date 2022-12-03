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
        Schema::create('uo_stocks', function (Blueprint $table) {
            $table->unsignedInteger('company_id')->index();
            $table->string('material_code', 10)->index();
            $table->unsignedInteger('plant_id')->index();
            $table->double('stock', 18, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uo_stocks');
    }
};
