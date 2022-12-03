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
        Schema::create('opname_items', function (Blueprint $table) {
            $table->unsignedInteger('opname_id')->index();
            $table->string('material_code', 10);
            $table->text('material_name');
            $table->decimal('qty_first', 18, 3)->default(0);
            $table->decimal('qty_update', 18, 3)->default(0);
            $table->decimal('qty_final', 18, 3)->default(0);
            $table->decimal('qty_sap', 18, 3)->default(0);
            $table->string('uom_first', 6)->default('');
            $table->string('uom_update', 6)->default('');
            $table->string('uom_final', 6)->default('');
            $table->string('uom_sap', 6)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opname_items');
    }
};
