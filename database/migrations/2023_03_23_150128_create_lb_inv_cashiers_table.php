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
        Schema::create('lb_inv_cashiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('material_logbook_id')->index();
            $table->string('frekuensi',6)->comment('Daily or Weekly');
			$table->tinyInteger('status')->comment('1 = active, 0 = Unactive');
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
        Schema::dropIfExists('lb_inv_cashiers');
    }
};
