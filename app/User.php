<?php

/*
 * Copyright 2016-2018 Appster Information Pvt. Ltd. 
 * All rights reserved.
 * File: User.php
 * CodeLibrary/Project: NA/Beautyjunkie
 * Author:Abhijeet
 * CreatedOn: date (10/04/2018) 
 */

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'updated_at','deleted_at', 'created_at'
    ];
}
