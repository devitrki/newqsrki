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
        Schema::create('uo_category_price_details', function (Blueprint $table) {
            $table->unsignedInteger('uo_category_price_id')->index();
            $table->unsignedInteger('uo_material_id')->index();
            $table->decimal('price', 18, 0)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('uo_category_price_details');
    }
};
