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

class CreateBeauticianQualificationsTable extends Migration
{
    /**
     * Run the migrations.
     * @table beautician_qualifications
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beautician_qualifications', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('qualification');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index(["user_id"], 'user_id');


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
       Schema::dropIfExists('beautician_qualifications');
     }
}
