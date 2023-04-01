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
        Schema::create('lb_stock_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_app_review_id')->index();
            $table->string('month', 2);
            $table->string('year', 4);
            $table->unsignedInteger('material_logbook_id')->index();
            $table->string('no_po', 50);
            $table->double('stock_initial', 8, 3)->default(0);
            $table->double('stock_in_gr', 8, 3)->default(0);
            $table->double('stock_in_tf', 8, 3)->default(0);
            $table->double('stock_out_used', 8, 3)->default(0);
            $table->double('stock_out_waste', 8, 3)->default(0);
            $table->double('stock_out_tf', 8, 3)->default(0);
            $table->double('stock_last', 8, 3)->default(0);
            $table->text('description')->nullable();
            $table->string('pic');
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
        Schema::dropIfExists('lb_stock_cards');
    }
};
