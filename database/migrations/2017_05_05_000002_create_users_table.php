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

class CreateBeauticianKitsTable extends Migration
{
    /**
     * Run the migrations.
     * @table beautician_kits
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beautician_kits', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('kit_name');

            $table->index(["user_id"], 'user_id');
            $table->softDeletes();
            $table->timestamps();


            $table->foreign('user_id', 'user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists('beautician_kits');
     }
}
