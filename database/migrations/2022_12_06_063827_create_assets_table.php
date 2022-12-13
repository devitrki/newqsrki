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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('number', 20)->index();
            $table->string('number_sub', 20);
            $table->unsignedInteger('plant_id')->index();
            $table->text('description');
            $table->text('spec_user')->nullable();
            $table->integer('qty_web');
            $table->string('uom', 10);
            $table->string('cost_center', 100);
            $table->string('cost_center_code', 15)->nullable();
            $table->text('remark')->nullable();
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
        Schema::dropIfExists('assets');
    }
};
