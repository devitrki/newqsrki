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
        Schema::table('lb_mon_sls_cas_dets', function (Blueprint $table) {
            $table->double('shopee_food', 18, 2)->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lb_mon_sls_cas_dets', function (Blueprint $table) {
            $table->dropColumn('shopee_food');
        });
    }
};
