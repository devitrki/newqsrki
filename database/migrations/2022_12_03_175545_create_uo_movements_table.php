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
        Schema::create('uo_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('document_number');
            $table->unsignedInteger('plant_id_sender')->index();
            $table->unsignedInteger('plant_id_receiver')->index();
            $table->date('date');
            $table->integer('type')->index()->comment('101 = Good Receipt +, 102 = Good Receipt -, 201 = Sales +, 202 = Sales -, 301 = GI Transfer -, 302 = GI Transfer +, 401 = GR Transfer +, 402 = GR Transfer -, 501 = Stock Adjustment +, 502 = Stock Adjusment -');
            $table->unsignedInteger('uo_vendor_id')->index()->nullable();
            $table->double('subtotal', 18, 0)->default(0);
            $table->double('tax', 18, 0)->default(0);
            $table->double('total', 18, 0)->default(0);
            $table->text('note')->nullable();
            $table->smallInteger('is_reverse')->default(0)->comment('0 = Not Reverse, 1 = Reverse');
            $table->unsignedInteger('reverse_id')->index()->nullable();
            $table->string('pic_sender')->nullable();
            $table->string('pic_receiver')->nullable();
            $table->smallInteger('gr_status')->default(0)->comment('1 = GR with remaining, 2 = All GR done');
            $table->unsignedInteger('uo_movement_id_gr')->index()->nullable();
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
        Schema::dropIfExists('uo_movements');
    }
};
