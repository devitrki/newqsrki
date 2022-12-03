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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedInteger('profile_id')->index();
            $table->tinyInteger('status')->default('1')->comment('0 = Blocked, 1 = Unactive, 2 = Active');
            $table->unsignedInteger('languange_id')->index();
            $table->unsignedInteger('company_id')->index();
            $table->string('created_by', 100);
            $table->dateTime('last_login')->nullable();
            $table->string('last_login_ip', 20)->nullable();
            $table->tinyInteger('flag_change_pass')->default(0)->comment('0 = not change, 1 = must change');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
