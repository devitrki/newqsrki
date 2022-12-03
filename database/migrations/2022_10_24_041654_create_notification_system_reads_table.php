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
        Schema::create('notification_system_reads', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('notification_system_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->tinyInteger('read')->default(0)->comment('0 = not yet read, 1 = read');
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
        Schema::dropIfExists('notification_system_reads');
    }
};
