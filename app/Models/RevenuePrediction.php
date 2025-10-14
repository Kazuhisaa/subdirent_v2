<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenuePrediction extends Model
{
    //

    protected $table = 'historical_revenues';


    protected $fillable = [
     'year',
     'month',
     'active_contracts',
     'new_contracts',
     'expired_contracts',
     'prev_month_revenue',
     'monthly_revenue'
    ];
}
