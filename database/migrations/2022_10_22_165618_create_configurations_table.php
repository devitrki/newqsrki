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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index()->nullable();
            $table->unsignedInteger('configuration_group_id')->index();
            $table->string('for', 50)->comment('field for different between type');
            $table->string('type', 50)->comment('field for different between input. select or text input');
            $table->string('label', 50);
            $table->string('description', 100);
            $table->string('key', 50);
            $table->longText('value')->nullable();
            $table->text('option')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configurations');
    }
};
