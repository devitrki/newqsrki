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
        Schema::create('asset_request_mutations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('number', 20)->index();
            $table->string('number_sub', 20);
            $table->text('description');
            $table->text('spec_user')->nullable();
            $table->integer('qty_web');
            $table->integer('qty_mutation');
            $table->string('uom', 10);
            $table->text('remark')->nullable();
            $table->string('req_number', 20)->index();
            $table->string('req_number_sub', 20);
            $table->text('req_description');
            $table->text('req_spec_user');
            $table->integer('req_qty_web');
            $table->integer('req_qty_mutation');
            $table->string('req_uom', 10);
            $table->text('req_remark');
            $table->unsignedInteger('from_plant_id')->index();
            $table->string('from_cost_center', 100);
            $table->string('from_cost_center_code', 15);
            $table->unsignedInteger('to_plant_id')->index();
            $table->string('to_cost_center', 100);
            $table->string('to_cost_center_code', 15);
            $table->dateTime('date_submit', 0)->nullable();
            $table->dateTime('date_cancel', 0)->nullable();
            $table->dateTime('date_approve_hod', 0)->nullable();
            $table->dateTime('date_unapprove_hod', 0)->nullable();
            $table->dateTime('date_confirmation_validator', 0)->nullable();
            $table->dateTime('date_reject_validator', 0)->nullable();
            $table->dateTime('date_send', 0)->nullable();
            $table->dateTime('date_reject_dc', 0)->nullable();

            /*
                Step request asset mutation
                1 = submit
                2 = cancel
                3 = approve hod
                4 = unapprove hod
                5 = confirmation validator
                6 = reject by validator
                7 = confirmation send dc
                8 = reject by dc
            */

            $table->tinyInteger('step_request')->default(1);
            $table->string('step_request_desc', 150);
            $table->string('level_request', 150);
            $table->unsignedInteger('level_request_id')->index();

            $table->tinyInteger('status')->default(0)->comment('0 = request in progress, 1 = request finish');

            $table->unsignedInteger('asset_validator_id')->index();
            $table->unsignedInteger('user_id')->index()->comment("id user request");
            $table->unsignedInteger('assign_asset_validator_id')->index()->comment("id validator assign")->nullable();
            $table->text('note_request')->nullable();
            $table->text('note_rejected')->nullable();
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
        Schema::dropIfExists('asset_request_mutations');
    }
};
