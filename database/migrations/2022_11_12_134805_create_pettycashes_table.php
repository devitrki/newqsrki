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
        Schema::create('pettycashes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->bigInteger('transaction_id')->index();
            $table->string('document_number', 15)->nullable();
            $table->string('document_po', 15)->nullable();
            $table->string('type_id', 15);
            $table->smallInteger('type');
            $table->date('transaction_date');
            $table->string('pic', 150);
            $table->string('voucher_number', 150);
            $table->unsignedInteger('plant_id')->index();
            $table->decimal('debit', 18, 0)->default(0);
            $table->decimal('kredit', 18, 0)->default(0);
            $table->decimal('saldo', 18, 0)->default(0);
            $table->text('remark')->nullable();
            $table->text('description')->nullable();
            $table->string('gl_code', 15);
            $table->string('gl_desc', 50);
            $table->smallInteger('approve');
            $table->smallInteger('submit');
            $table->smallInteger('order_number');
            $table->string('receive_pic', 150)->nullable();
            $table->date('receive_date')->nullable();
            $table->text('description_reject')->nullable();
            $table->dateTime('approved_at', 0)->nullable();
            $table->dateTime('unapproved_at', 0)->nullable();
            $table->dateTime('submited_at', 0)->nullable();
            $table->dateTime('rejected_at', 0)->nullable();
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
        Schema::dropIfExists('pettycashes');
    }
};
