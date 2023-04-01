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
        Schema::create('lb_prod_qualities', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_prod_plan_id')->index();
            $table->string('fryer', 5)->nullable();
            $table->string('tpm')->nullable();
            $table->string('temp')->nullable();
            $table->string('oil_status')->nullable();
            $table->string('filtration')->nullable();
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
        Schema::dropIfExists('lb_prod_qualities');
    }
};
