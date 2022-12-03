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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->tinyInteger('work_at')->default('0')->comment('0 = HO, 1 = Outlet, 2 = DC');
            $table->unsignedInteger('company_id')->index();
            $table->unsignedInteger('country_id')->index();
            $table->unsignedInteger('department_id')->index();
            $table->unsignedInteger('position_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
};
