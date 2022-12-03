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
        Schema::create('mass_clearings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->text('description');
            $table->dateTime('time_process_start', 0)->nullable();
            $table->dateTime('time_process_finish', 0)->nullable();
            $table->string('filename', 50)->nullable();
            $table->smallInteger('status_generate')->comment('0 = waiting, 1 = process, 2 = finish')->default(0);
            $table->unsignedInteger('user_id')->index();
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
        Schema::dropIfExists('mass_clearings');
    }
};
