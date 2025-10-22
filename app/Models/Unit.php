<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Application;
class Unit extends Model
{
    use HasFactory;
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'unit_code',
        'description',
        'floor_area',
        'monthly_rent',
        'unit_price',
        'contract_years',
        'status',
        'files',
        'bathroom',   // ✅ add this
        'bedroom',  
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

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
