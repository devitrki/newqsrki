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
        Schema::create('waste_items', function (Blueprint $table) {
            $table->unsignedInteger('waste_id')->index();
            $table->string('material_code', 10);
            $table->text('material_name');
            $table->decimal('qty', 18, 3)->default(0);
            $table->string('uom', 6)->default('');
            $table->text('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('waste_items');
    }
};
