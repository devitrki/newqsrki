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
        Schema::create('special_gls', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->char('special_gl', 1)->index();
            $table->string('payment_type', 50);
            $table->string('reference');
            $table->string('sap_code', 3);
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
        Schema::dropIfExists('special_gls');
    }
};
