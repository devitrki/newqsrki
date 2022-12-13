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
        Schema::create('asset_sos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('month', 2)->index();
            $table->string('month_label', 50);
            $table->string('year', 4)->index();
            $table->tinyInteger('status_generate_outlet')->default(0)->comment('0 = not yet, 1 = done');
            $table->tinyInteger('status_submit_outlet')->default(0)->comment('0 = not yet, 1 = done');
            $table->tinyInteger('status_generate_dc')->default(0)->comment('0 = not yet, 1 = done');
            $table->tinyInteger('status_submit_dc')->default(0)->comment('0 = not yet, 1 = done');
            $table->longText('send_am_outlet')->nullable();
            $table->longText('send_am_dc')->nullable();
            $table->tinyInteger('send_depart_asset_outlet')->default(0)->comment('0 = not yet, 1 = done');
            $table->tinyInteger('send_depart_asset_dc')->default(0)->comment('0 = not yet, 1 = done');
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
        Schema::dropIfExists('asset_sos');
    }
};
