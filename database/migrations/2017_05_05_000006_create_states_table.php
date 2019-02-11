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

class CreateBeauticianDetailsTable extends Migration
{
    /**
     * Run the migrations.
     * @table beautician_details
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beautician_details', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('abn', 11);
            $table->string('business_name', 100);
            $table->string('instagram_link', 250);
            $table->string('police_check_certificate', 100);
            $table->text('business_description');
            $table->tinyInteger('cruelty_free_makeup')->default('0');
            $table->unsignedTinyInteger('work_radius');

            $table->index(["user_id"], 'user_id');
            $table->softDeletes();
            $table->timestamps();


            $table->foreign('user_id', 'user_id')
                ->references('id')->on('users')
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
       Schema::dropIfExists('beautician_details');
     }
}
