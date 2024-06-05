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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'member'])->default('member')->after('remember_token');
            $table->enum('member_type', ['free', 'paid'])->default('free')->after('role');
            $table->string('stripe_customer_id')->nullable()->after('member_type');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->dropColumn('member_type');
            $table->dropColumn('stripe_customer_id');
            $table->dropSoftDeletes();
        });
    }
};
