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
        Schema::create('material_outlets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('code', 10);
            $table->text('description');
            $table->smallInteger('opname')->default(1)->comment('0 = not checklist, 1 = checklist');
            $table->string('opname_uom', 6);
            $table->smallInteger('waste')->default(1)->comment('0 = not checklist, 1 = checklist');
            $table->string('waste_uom', 6);
            $table->smallInteger('waste_flag')->default(0)->comment('0 = not checklist, 1 = checklist');
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
        Schema::dropIfExists('material_outlets');
    }
};
