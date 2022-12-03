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
        Schema::create('uo_deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('uo_vendor_id')->index();
            $table->string('document_number');
            $table->date('deposit_date')->index();
            $table->text('image')->nullable();
            $table->string('richeese_bank', 50);
            $table->smallInteger('type_deposit')->comment('1 = deposit cash, 2 = trasnfer bank');
            $table->string('transfer_bank')->nullable();
            $table->string('transfer_bank_account')->nullable();
            $table->string('transfer_bank_account_name')->nullable();
            $table->double('deposit_nominal', 18, 0);
            $table->smallInteger('submit')->default(0)->comment('0 = not yet, 1 = already');
            $table->smallInteger('confirmation_fa')->default(0)->comment('0 = waiting, 1 = approve, 2 = reject');
            $table->string('reject_description')->nullable();
            $table->string('created_by');
            $table->unsignedInteger('created_id')->index();
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
        Schema::dropIfExists('uo_deposits');
    }
};
