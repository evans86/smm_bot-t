<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function (Blueprint $table) {
            $table->index('user_id', 'order_user_idx');
//            $table->foreign('user_id', 'order_user_fk')->on('user')->references('id');

//            $table->index('country_id', 'order_country_idx');
//            $table->foreign('country_id', 'order_country_fk')->on('country')->references('id');

//            $table->index('proxy_id', 'order_proxy_idx');
//            $table->foreign('proxy_id', 'order_proxy_fk')->on('proxy')->references('id');
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
