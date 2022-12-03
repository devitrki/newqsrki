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
        Schema::create('gr_plant_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('gr_plant_id')->index();
            $table->unsignedInteger('material_id')->index();
            $table->decimal('qty_gr', 10, 3);
            $table->decimal('qty_b4_gr', 10, 3);
            $table->decimal('qty_po', 10, 3);
            $table->decimal('qty_remaining', 10, 3);
            $table->string('uom', 10);
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
        Schema::dropIfExists('gr_plant_items');
    }
};
