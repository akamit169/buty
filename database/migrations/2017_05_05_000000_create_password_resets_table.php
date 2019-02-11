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

class CreateBeauticianAvailabilityScheduleTable extends Migration
{
    /**
     * Run the migrations.
     * @table beautician_availability_schedule
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beautician_availability_schedule', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('beautician_id');
            $table->dateTime('start_datetime')->nullable()->default(null);
            $table->dateTime('end_datetime')->nullable()->default(null);
            $table->tinyInteger('is_available')->default('1')->comment('0=>\'Not Available\', 1=>\'Available\'');

            $table->index(["beautician_id"], 'beautician_id');

            $table->index(["beautician_id"], 'beautician_id_2');
            $table->softDeletes();
            $table->timestamps();


            $table->foreign('beautician_id', 'beautician_id')
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
       Schema::dropIfExists('beautician_availability_schedule');
     }
}
