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
        Schema::create('material_convertions', function (Blueprint $table) {
            $table->unsignedInteger('material_id')->index();
            $table->decimal('base_qty', 14, 6);
            $table->string('base_uom', 6);
            $table->decimal('convertion_qty', 14, 6);
            $table->string('convertion_uom', 6);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_convertions');
    }
};
