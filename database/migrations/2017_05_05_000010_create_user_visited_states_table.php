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

class CreateBookingDisputesTable extends Migration
{
    /**
     * Run the migrations.
     * @table booking_disputes
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_disputes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('customer_booking_id');
            $table->string('reason');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index(["customer_booking_id"], 'customer_booking_id');
            $table->softDeletes();


            $table->foreign('customer_booking_id', 'customer_booking_id')
                ->references('id')->on('customer_bookings')
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
       Schema::dropIfExists('booking_disputes');
     }
}
