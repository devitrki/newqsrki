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
        Schema::create('asset_mutations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('number', 20)->index();
            $table->string('number_sub', 20);
            $table->text('description');
            $table->text('spec_user');
            $table->integer('qty_web');
            $table->integer('qty_mutation');
            $table->string('uom', 10);
            $table->string('req_number', 20)->index();
            $table->string('req_number_sub', 20);
            $table->text('req_description');
            $table->text('req_spec_user');
            $table->integer('req_qty_web');
            $table->integer('req_qty_mutation');
            $table->string('req_uom', 10);
            $table->text('req_remark')->nullable();


            $table->text('remark')->nullable();
            $table->unsignedInteger('from_plant_id')->index();
            $table->string('from_cost_center', 100);
            $table->string('from_cost_center_code', 15);
            $table->unsignedInteger('to_plant_id')->index();
            $table->string('to_cost_center', 100);
            $table->string('to_cost_center_code', 15);
            $table->string('pic_sender', 50)->nullable();
            $table->string('pic_receiver', 50)->nullable();

            $table->dateTime('date_request', 0)->nullable();
            $table->dateTime('date_cancel_request', 0)->nullable();
            $table->dateTime('date_approve_first', 0)->nullable();
            $table->dateTime('date_unapprove_first', 0)->nullable();
            $table->dateTime('date_confirmation_validator', 0)->nullable();
            $table->dateTime('date_reject_validator', 0)->nullable();
            $table->dateTime('date_approve_second', 0)->nullable();
            $table->dateTime('date_unapprove_second', 0)->nullable();
            $table->dateTime('date_approve_third', 0)->nullable();
            $table->dateTime('date_unapprove_third', 0)->nullable();
            $table->dateTime('date_confirmation_sender', 0)->nullable();
            $table->dateTime('date_reject_sender', 0)->nullable();
            $table->dateTime('date_accept_receiver', 0)->nullable();
            $table->dateTime('date_reject_receiver', 0)->nullable();

            $table->string('requestor', 50);
            $table->string('level_request_first', 150);
            $table->unsignedInteger('level_request_first_id')->index();
            $table->string('level_request_second', 150);
            $table->unsignedInteger('level_request_second_id')->index();
            $table->string('level_request_third', 150);
            $table->unsignedInteger('level_request_third_id')->index();
            $table->unsignedInteger('asset_validator_id')->index();
            $table->unsignedInteger('user_id')->index()->comment("id user request");
            $table->unsignedInteger('assign_asset_validator_id')->index()->comment("id validator assign")->nullable();

            $table->string('sender_cost_center', 150)->nullable();
            $table->unsignedInteger('sender_cost_center_id')->index()->nullable();
            $table->string('receiver_cost_center', 150)->nullable();
            $table->unsignedInteger('receiver_cost_center_id')->index()->nullable();

            $table->text('note_request')->nullable();
            $table->string('condition_send', 30)->nullable()->comment("good condition / bad condition");
            $table->string('condition_receive', 30)->nullable()->comment("good condition / bad condition");

            $table->dateTime('date_send_est', 0)->nullable();
            $table->text('reason_rejected', 0)->nullable();

            /*
                status mutation
                1 = request
                2 = cancel request
                3 = approve approver 1
                4 = unapprove approver 2
                5 = confirmation validator
                6 = reject by validator
                7 = approve approver 2
                8 = unapprove approver 2
                9 = approve approver 3
                10 = unapprove approver 3
                11 = confirmation send sender
                12 = reject sender
                13 = accept receiver
                14 = reject receiver
            */

            $table->tinyInteger('status_mutation')->default(1);
            $table->string('status_mutation_desc', 30);
            $table->tinyInteger('status_changed')->default(0)->comment('0 = Not Changed, 1 = Changed');
            $table->tinyInteger('status')->default(0)->comment('0 = mutation in progress, 1 = mutation finish');
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
        Schema::dropIfExists('asset_mutations');
    }
};
