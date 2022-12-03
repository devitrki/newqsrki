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
        Schema::create('gi_plant_items', function (Blueprint $table) {
            $table->unsignedInteger('gi_plant_id')->index();
            $table->unsignedInteger('material_id')->index();
            $table->decimal('qty', 10, 3);
            $table->string('uom', 10);
            $table->text('note');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gi_plant_items');
    }
};
