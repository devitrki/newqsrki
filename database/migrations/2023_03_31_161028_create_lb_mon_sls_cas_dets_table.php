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
        Schema::create('lb_mon_sls_cas_dets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_mon_sls_cas_id')->index();
            $table->string('cashier_no');
            $table->string('cashier_name')->nullable();
            $table->double('opening_cash', 18, 2)->default('500000');
            $table->double('total_sales', 18, 2)->default('0');
            $table->double('bca', 18, 2)->default('0');
            $table->double('mandiri', 18, 2)->default('0');
            $table->double('go_pay', 18, 2)->default('0');
            $table->double('grab_pay', 18, 2)->default('0');
            $table->double('gobiz', 18, 2)->default('0');
            $table->double('ovo', 18, 2)->default('0');
            $table->double('shoope_pay', 18, 2)->default('0');
            $table->double('dana', 18, 2)->default('0');
            $table->double('voucher', 18, 2)->default('0');
            $table->double('delivery_sales', 18, 2)->default('0');
            $table->double('drive_thru', 18, 2)->default('0');
            $table->double('compliment', 18, 2)->default('0');
            $table->double('total_cash_hand', 18, 2)->default('0');
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
        Schema::dropIfExists('lb_mon_sls_cas_dets');
    }
};
