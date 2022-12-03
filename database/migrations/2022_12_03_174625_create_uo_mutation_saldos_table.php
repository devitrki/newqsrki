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
        Schema::create('uo_mutation_saldos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('document_number');
            $table->date('date')->index();
            $table->unsignedInteger('uo_vendor_id_sender')->index();
            $table->unsignedInteger('uo_vendor_id_receiver')->index();
            $table->double('nominal', 18, 0);
            $table->text('description')->nullable();
            $table->string('created_by');
            $table->unsignedInteger('created_id')->index();
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
        Schema::dropIfExists('uo_mutation_saldos');
    }
};
