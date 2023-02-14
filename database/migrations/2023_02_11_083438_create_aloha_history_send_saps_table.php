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
        Schema::create('aloha_history_send_saps', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('plant_id')->index();
            $table->date('date');
            $table->decimal('selisih', 18, 2)->default(0);
            $table->decimal('total_payments', 18 , 2)->default(0);
            $table->decimal('total_sales', 18 , 2)->default(0);
            $table->smallInteger('send')->comment('0 = not send, 1 = send');
            $table->text('description');
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
        Schema::dropIfExists('aloha_history_send_saps');
    }
};
