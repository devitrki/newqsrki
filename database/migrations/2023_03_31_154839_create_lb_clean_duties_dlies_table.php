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
        Schema::create('lb_clean_duties_dlies', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_clean_duties_id')->index();
            $table->string('task');
            $table->tinyInteger('opening')->default(0)->comment('0 = Not Checklis, 1 = Checklis');
            $table->tinyInteger('closing')->default(0)->comment('0 = Not Checklis, 1 = Checklis');
            $table->tinyInteger('midnite')->default(0)->comment('0 = Not Checklis, 1 = Checklis');
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
        Schema::dropIfExists('lb_clean_duties_dlies');
    }
};
