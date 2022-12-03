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
        Schema::create('mass_clearing_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('mass_clearing_id')->index();
            $table->string('bank_in_bank_gl', 15)->index();
            $table->date('bank_in_date');
            $table->text('bank_in_description')->nullable();
            $table->string('sales_date', 50);
            $table->string('sales_month', 2);
            $table->string('sales_year', 4);
            $table->boolean('multiple_date');
            $table->char('special_gl', 1)->index();
            $table->unsignedInteger('plant_id')->index();
            $table->decimal('bank_in_nominal', 18, 0)->default(0);
            $table->decimal('bank_in_charge', 18, 0)->default(0);
            $table->decimal('nominal_sales', 18, 0)->default(0);
            $table->decimal('selisih', 18, 0)->default(0);
            $table->decimal('selisih_percent', 18, 2)->default(0);
            $table->smallInteger('status_process')->comment('0 = waiting, 1 = process, 2 = finish')->default(0);
            $table->smallInteger('status_generate')->comment('0 = -, 1 = yes, 2 = no')->default(0);
            $table->text('description')->comment('description from status generate')->nullable();
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
        Schema::dropIfExists('mass_clearing_details');
    }
};
