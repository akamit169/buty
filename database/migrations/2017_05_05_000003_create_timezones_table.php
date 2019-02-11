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

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     * @table notifications
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedTinyInteger('type');
            $table->unsignedInteger('sender_id');
            $table->unsignedInteger('recipient_id');
            $table->unsignedTinyInteger('is_read')->default('0');

            $table->index(["recipient_id"], 'recipient_id');

            $table->index(["sender_id"], 'sender_id');
            $table->softDeletes();
            $table->timestamps();


            $table->foreign('sender_id', 'sender_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('recipient_id', 'recipient_id')
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
       Schema::dropIfExists('notifications');
     }
}
