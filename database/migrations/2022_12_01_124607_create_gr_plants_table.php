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
        Schema::create('gr_plants', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('document_number', 15);
            $table->string('delivery_number', 15);
            $table->string('posto_number', 15);
            $table->date('date');
            $table->unsignedInteger('receiving_plant_id')->index();
            $table->unsignedInteger('issuing_plant_id')->index()->nullable();
            $table->string('recepient', 50)->nullable();
            $table->string('gr_from', 50)->nullable();
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
        Schema::dropIfExists('gr_plants');
    }
};
