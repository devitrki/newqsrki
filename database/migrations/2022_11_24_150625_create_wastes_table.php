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
        Schema::create('wastes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('document_number', 15)->nullable();
            $table->unsignedInteger('plant_id')->index();
            $table->date('date');
            $table->dateTime('posting_date', 0)->nullable();
            $table->string('pic');
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
        Schema::dropIfExists('wastes');
    }
};
