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
        Schema::create('asset_so_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('asset_so_plant_id')->index();
            $table->string('number', 20)->index();
            $table->string('number_sub', 20);
            $table->text('description');
            $table->text('spec_user');
            $table->integer('qty_web');
            $table->integer('qty_so');
            $table->integer('qty_selisih')->default(0);
            $table->string('uom', 10);
            $table->text('remark')->nullable();
            $table->text('remark_so')->nullable();
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
        Schema::dropIfExists('asset_so_details');
    }
};
