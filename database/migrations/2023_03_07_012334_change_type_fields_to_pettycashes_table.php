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
        Schema::table('pettycashes', function (Blueprint $table) {
            $table->decimal('debit', 18, 2)->change();
            $table->decimal('kredit', 18, 2)->change();
            $table->decimal('saldo', 18, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pettycashes', function (Blueprint $table) {
            $table->decimal('debit', 18, 0)->change();
            $table->decimal('kredit', 18, 0)->change();
            $table->decimal('saldo', 18, 0)->change();
        });
    }
};
