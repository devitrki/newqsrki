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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('parent_id')->default(0)->comment('0 = root, >0 = have parent');
            $table->tinyInteger('type')->comment('1 = file, 2 = folder, 3 = module');
            $table->string('path', 20);
            $table->string('name', 100);
            $table->string('description')->nullable();
            $table->string('url')->nullable();
            $table->string('icon', 30)->nullable();
            $table->string('permission_menu', 50)->nullable();
            $table->tinyInteger('flag_end')->default(0)->comment('0 = not end, 1 = end');
            $table->tinyInteger('sort_order');
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
        Schema::dropIfExists('menus');
    }
};
