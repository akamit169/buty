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

class CreateBeauticianServicesTable extends Migration
{
    /**
     * Run the migrations.
     * @table beautician_services
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beautician_services', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('beautician_id');
            $table->unsignedSmallInteger('service_id');
            $table->unsignedSmallInteger('parent_service_id');
            $table->unsignedInteger('duration')->comment('in mins');
            $table->decimal('cost', 7, 2);
            $table->text('description');
            $table->string('tip');
            $table->unsignedTinyInteger('no_of_sessions');
            $table->unsignedSmallInteger('time_btw_sessions');
            $table->float('discount');
            $table->date('discount_startdate')->nullable()->default(null);
            $table->date('discount_enddate')->nullable()->default(null);

            $table->index(["service_id"], 'service_id');

            $table->index(["beautician_id"], 'beautician_id');

            $table->index(["parent_service_id"], 'parent_service_id');
            $table->softDeletes();
            $table->timestamps();


            $table->foreign('beautician_id', 'beautician_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('service_id', 'service_id')
                ->references('id')->on('services')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('parent_service_id', 'parent_service_id')
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
       Schema::dropIfExists('beautician_services');
     }
}
