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
        Schema::create('gr_vendors', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('gr_number', 15);
            $table->string('po_number', 15);
            $table->string('ref_number', 100);
            $table->string('vendor_name', 100);
            $table->string('vendor_id', 20);
            $table->smallInteger('item_number');
            $table->string('material_code', 50)->nullable();
            $table->text('material_desc');
            $table->unsignedInteger('plant_id')->index();
            $table->date('po_date');
            $table->date('posting_date');
            $table->decimal('qty_gr', 10, 3);
            $table->decimal('qty_remaining_po', 10, 3);
            $table->decimal('qty_po', 10, 3);
            $table->decimal('qty_remaining', 10, 3);
            $table->string('uom', 10);
            $table->string('recepient', 100);
            $table->decimal('batch', 10, 3)->nullable();
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
        Schema::dropIfExists('gr_vendors');
    }
};
