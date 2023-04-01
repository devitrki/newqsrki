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
        Schema::create('lb_dly_inv_kitchens', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_app_review_id')->index();
            $table->string('product_name', 100);
            $table->string('uom', 20);
            $table->string('frekuensi',6);
            $table->double('stock_opening', 8, 3)->default(0);
            $table->double('stock_in', 8, 3)->default(0);
            $table->double('stock_out', 8, 3)->default(0);
            $table->double('stock_closing', 8, 3)->default(0);
            $table->string('note')->nullable();
            $table->string('last_update', 100);
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
        Schema::dropIfExists('lb_dly_inv_kitchens');
    }
};
