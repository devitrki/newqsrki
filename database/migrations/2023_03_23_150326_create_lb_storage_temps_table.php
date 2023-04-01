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
        Schema::create('lb_storage_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('name');
            $table->integer('top_value');
            $table->integer('bottom_value');
            $table->integer('top_value_center');
            $table->integer('bottom_value_center');
            $table->integer('interval');
            $table->string('uom', 20)->nullable();
            $table->tinyInteger('status')->comment('1 = active, 0 = Unactive');
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
        Schema::dropIfExists('lb_storage_temps');
    }
};
