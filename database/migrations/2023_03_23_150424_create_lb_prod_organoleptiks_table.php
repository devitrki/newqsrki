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
        Schema::create('lb_prod_organoleptiks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('product');
            $table->string('desc_taste')->nullable()->after('product');
            $table->string('desc_aroma')->nullable()->after('desc_taste');
            $table->string('desc_texture')->nullable()->after('desc_aroma');
            $table->string('desc_color')->nullable()->after('desc_texture');
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
        Schema::dropIfExists('lb_prod_organoleptiks');
    }
};
