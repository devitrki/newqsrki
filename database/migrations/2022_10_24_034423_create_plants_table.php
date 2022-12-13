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
        Schema::create('plants', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5);
            $table->string('short_name', 50);
            $table->string('description')->nullable();
            $table->string('initital', 5)->comment('RF or DC');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            // outlet
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('pos_id')->index()->nullable();
            $table->tinyInteger('hours')->comment('12 hours OR 24 hours')->nullable();
            $table->tinyInteger('drivethru')->comment('status drive thru, 0 = false, 1 = true')->nullable();
            $table->string('cost_center', 15)->nullable();
            $table->string('cost_center_desc', 50)->nullable();
            $table->string('customer_code', 10)->nullable();
            $table->tinyInteger('price_category')->nullable();
            $table->unsignedInteger('dc_id')->comment('Supply Outlet, Relation to plant for get dc data')->index()->nullable();
            $table->unsignedInteger('area_id')->index()->nullable();
            $table->string('sloc_id_gr', 15)->default('S001');
            $table->string('sloc_id_gr_vendor', 15)->default('S001');
            $table->string('sloc_id_waste', 15)->default('S001');
            $table->string('sloc_id_asset_mutation', 15)->default('r100');
            $table->string('sloc_id_current_stock', 15)->default('S001');
            // status & type
            $table->tinyInteger('type')->comment('1 = Outlet, 2 = DC');
            $table->tinyInteger('status')->comment('0 = Not Active, 1 = Active');
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
        Schema::dropIfExists('plants');
    }
};
