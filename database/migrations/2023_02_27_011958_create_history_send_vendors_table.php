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
        Schema::create('history_send_vendors', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->date('date');
            $table->unsignedInteger('send_vendor_id')->index();
            $table->tinyInteger('status');
            $table->text('description');
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
        Schema::dropIfExists('history_send_vendors');
    }
};
