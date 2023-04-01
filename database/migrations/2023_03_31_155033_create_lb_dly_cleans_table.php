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
        Schema::create('lb_dly_cleans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lb_app_review_id')->index();
            $table->string('task');
            $table->string('section', 30);
            $table->string('frekuensi',25)->comment('1 jam dst');
            $table->tinyInteger('shift');
            $table->tinyInteger('checklis_1')->default(0)->comment('0 = Not Checklis, 1 = Checklis');
            $table->tinyInteger('checklis_2')->default(0)->comment('0 = Not Checklis, 1 = Checklis');
            $table->tinyInteger('checklis_3')->default(0)->comment('0 = Not Checklis, 1 = Checklis');
            $table->tinyInteger('checklis_4')->default(0)->comment('0 = Not Checklis, 1 = Checklis');
            $table->tinyInteger('checklis_5')->default(0)->comment('0 = Not Checklis, 1 = Checklis');
            $table->tinyInteger('checklis_6')->default(0)->comment('0 = Not Checklis, 1 = Checklis');
            $table->tinyInteger('checklis_7')->default(0)->comment('0 = Not Checklis, 1 = Checklis');
            $table->tinyInteger('checklis_8')->default(0)->comment('0 = Not Checklis, 1 = Checklis');
            $table->string('pic');
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
        Schema::dropIfExists('lb_dly_cleans');
    }
};
