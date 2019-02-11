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

class CreateFlaggedUsersTable extends Migration
{
    /**
     * Run the migrations.
     * @table flagged_users
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flagged_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('flagged_by');
            $table->unsignedInteger('flagged_user');
            $table->string('reason');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index(["flagged_by"], 'flagged_by');

            $table->index(["flagged_user"], 'flagged_user');


            $table->foreign('flagged_by', 'flagged_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('flagged_user', 'flagged_user')
                ->references('id')->on('users')
                ->onDelete('cascade')
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
       Schema::dropIfExists('flagged_users');
     }
}
