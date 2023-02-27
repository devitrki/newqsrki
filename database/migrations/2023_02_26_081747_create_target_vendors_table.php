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
        Schema::create('target_vendors', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('name', 150);
            $table->string('transfer_type', 150);
            $table->longText('configurations')->nullable();
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
        Schema::dropIfExists('target_vendors');
    }
};
