<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'unit_code',
        'description',
        'floor_area',
        'monthly_rent',
        'unit_price',
        'status',
        'files',
    ];

    protected $casts = [
        'floor_area'   => 'integer',
        'bathroom'     => 'integer',
        'bedroom'      => 'integer',
        'monthly_rent' => 'float',
        'unit_price'   => 'float',
        'files'        => 'array', 
    ];

    public function getMonthlyRentAttribute($value)
    {
        return number_format($value, 2, '.', ',');
    }

    public function getUnitPriceAttribute($value)
    {
        return number_format($value, 2, '.', ',');
    }
}
