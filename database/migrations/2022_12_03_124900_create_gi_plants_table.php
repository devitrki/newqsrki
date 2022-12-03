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
        Schema::create('gi_plants', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('document_number', 15)->nullable();
            $table->string('document_posto', 15)->nullable();
            $table->date('date');
            $table->string('issuer', 50);
            $table->string('requester', 50);
            $table->unsignedInteger('issuing_plant_id')->index();
            $table->unsignedInteger('receiving_plant_id')->index();
            $table->string('movement_type', 5);
            $table->text('json_sap')->nullable();
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
        Schema::dropIfExists('gi_plants');
    }
};
