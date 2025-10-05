<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //

      protected $fillable = [
       'first_name',
        'middle_name',
        'last_name',
        'email',
        'contact_num',
         'date',
         'time'
        ];

      
}
