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
        Schema::create('uo_saldo_vendor_histories', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedInteger('uo_vendor_id')->index();
            $table->smallInteger('transaction_type')->comment('0 = sales (-), 1 = deposit (+), 2 = mutasi saldo in (+), 3 = mutasi saldo out (-)');
            $table->unsignedInteger('transaction_id')->index();
            $table->double('nominal', 18, 0);
            $table->double('saldo', 18, 0);
            $table->text('description')->nullable();
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
        Schema::dropIfExists('uo_saldo_vendor_histories');
    }
};
