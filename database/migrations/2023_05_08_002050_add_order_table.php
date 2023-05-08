<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('user_org_id')->nullable();
            $table->string('balance_org')->nullable();
            $table->string('order_org_id')->nullable();
            $table->string('count')->nullable();
            $table->string('price')->nullable();
            $table->string('period')->nullable();
            $table->integer('proxy_id')->unsigned()->nullable();
            $table->string('type')->nullable();
            $table->integer('country_id')->unsigned()->nullable();
            $table->string('prolong_org_id')->nullable();
            $table->string('ip')->nullable();
            $table->string('host')->nullable();
            $table->string('port')->nullable();
            $table->string('user')->nullable();
            $table->string('pass')->nullable();
            $table->string('status_org')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
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
