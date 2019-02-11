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

class CreateBookingRatingsTable extends Migration
{
    /**
     * Run the migrations.
     * @table booking_ratings
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_ratings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('customer_booking_id');
            $table->unsignedInteger('rated_by');
            $table->unsignedInteger('rated_to');
            $table->float('rating');
            $table->string('comment');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index(["customer_booking_id"], 'customer_booking_id');

            $table->index(["rated_to"], 'rated_to');

            $table->index(["rated_by"], 'rated_by');
            $table->softDeletes();


            $table->foreign('customer_booking_id', 'customer_booking_id')
                ->references('id')->on('customer_bookings')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('rated_by', 'rated_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('rated_to', 'rated_to')
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
       Schema::dropIfExists('booking_ratings');
     }
}
