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
        Schema::create('lb_dly_wasteds', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_app_review_id')->index();
            $table->unsignedInteger('material_logbook_id')->index();
            $table->double('qty', 8, 3)->default(0);
            $table->string('uom')->nullable();
            $table->text('remark')->nullable();
            $table->string('time', 100);
            $table->string('last_update', 100);
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
        Schema::dropIfExists('lb_dly_wasteds');
    }
};
