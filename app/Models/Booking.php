<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

      protected $fillable = [
    'first_name',
    'middle_name',
    'last_name',
    'email',
    'contact_num',
    'date',
    'unit_id',
    'booking_time',
    'status,'
];

      
}
