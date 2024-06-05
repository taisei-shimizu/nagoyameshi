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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('image');
            $table->unsignedBigInteger('category_id');
            $table->integer('budget_lower')->unsigned();
            $table->integer('budget_upper')->unsigned();
            $table->time('opening_time');
            $table->time('closing_time');
            $table->string('closed_day');
            $table->string('postal_code');
            $table->string('address');
            $table->string('phone');
            $table->timestamps();
            $table->softDeletes();

            // カテゴリーテーブルとの外部キー制約
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
};
