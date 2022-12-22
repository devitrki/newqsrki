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
        Schema::create('mass_clearing_generates', function (Blueprint $table) {
            $table->unsignedInteger('mass_clearing_id')->index();
            $table->integer('no');
            $table->integer('item');
            $table->string('customer_code', 25);
            $table->date('bank_in_date');
            $table->char('special_gl', 1);
            $table->string('document_number');
            $table->decimal('ar_value', 18, 0)->default(0);
            $table->string('reference');
            $table->string('gl_account');
            $table->decimal('value', 18, 0)->default(0);
            $table->string('assigment');
            $table->string('tax_code', 5);
            $table->text('text');
            $table->string('cost_center');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mass_clearing_generates');
    }
};
