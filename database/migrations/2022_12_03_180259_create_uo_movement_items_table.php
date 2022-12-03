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
        Schema::create('uo_movement_items', function (Blueprint $table) {
            $table->unsignedInteger('uo_movement_id')->index();
            $table->string('material_code', 10);
            $table->string('material_name', 200);
            $table->string('material_uom', 10);
            $table->double('qty', 18, 2)->default(0);
            $table->double('qty_gr', 18, 2)->default(0);
            $table->double('price', 18, 0)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uo_movement_items');
    }
};
