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
        Schema::create('opnames', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('document_number', 15)->nullable();
            $table->unsignedInteger('plant_id')->index();
            $table->date('date');
            $table->dateTime('update_date', 0)->nullable();
            $table->dateTime('posting_date', 0)->nullable();
            $table->text('note')->nullable();
            $table->string('pic');
            $table->string('pic_update')->nullable();
            $table->smallInteger('update')->default(0)->comment('0 = not updated, 1 = updated');
            $table->smallInteger('submit')->default(0)->comment('0 = not yet submit, 1 = already submit');
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
        Schema::dropIfExists('opnames');
    }
};
