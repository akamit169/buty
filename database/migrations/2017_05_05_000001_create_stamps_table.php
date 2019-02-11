<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * CodeLibrary/Project: Beauty Junkie
 * Author: Abhijeet/Amit
 * CreatedOn: date (30/03/2018) 
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeauticianPortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     * @table beautician_portfolios
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beautician_portfolios', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedSmallInteger('service_id');
            $table->string('image', 100);
            $table->string('image_thumbnail', 100);

            $table->index(["service_id"], 'service_id');

            $table->index(["user_id"], 'user_id');
            $table->timestamps();


            $table->foreign('user_id', 'user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('service_id', 'service_id')
                ->references('id')->on('services')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists('beautician_portfolios');
     }
}
