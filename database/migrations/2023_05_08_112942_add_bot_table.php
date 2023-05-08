<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bot', function (Blueprint $table) {
            $table->id();
            $table->string('public_key')->nullable();
            $table->string('private_key')->nullable();
            $table->unsignedBigInteger('bot_id')->nullable();
            $table->integer('version')->default(1);
            $table->integer('category_id')->nullable();
            $table->integer('percent')->nullable();
            $table->string('api_key')->nullable();
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
        //
    }
}
