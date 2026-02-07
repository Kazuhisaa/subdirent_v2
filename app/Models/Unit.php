<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Application;
use App\Models\Tenant; // Import the Tenant model

class Unit extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'location',
        'unit_code',
        'description',
        'floor_area',
        'lot_size',
        'monthly_rent',
        'unit_price',
        'status',
        'files',
        'bathroom',
        'bedroom',
        'build_year'
    ];

    protected $casts = [
        'floor_area'   => 'integer',
        'lot_size'     => 'integer',
        'bathroom'     => 'integer',
        'bedroom'      => 'integer',
        'monthly_rent' => 'integer',
        'unit_price'   => 'integer',
         'build_year' => 'integer',
        'files'        => 'array',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function tenant() // Add this relationship
    {
        return $this->hasOne(Tenant::class);
    }
}
