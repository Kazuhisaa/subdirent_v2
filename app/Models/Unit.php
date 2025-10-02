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
        'status',
        'files',
    ];

    protected $casts = [
        'files' => 'array',
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
