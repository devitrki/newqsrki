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
        Schema::create('lb_rec_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_app_review_id')->index();
            $table->string('product', 150);
            $table->string('transport_temperature', 10);
            $table->string('transport_cleanliness', 100);
            $table->string('product_temperature', 10);
            $table->string('producer', 100);
            $table->string('country', 100);
            $table->string('supplier');
            $table->string('logo_halal', 100);
            $table->string('product_condition', 100);
            $table->string('production_code', 100);
            $table->double('product_qty', 8, 3)->default(0);
            $table->string('product_uom');
            $table->date('expired_date');
            $table->string('status', 50);
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
        Schema::dropIfExists('lb_rec_materials');
    }
};
