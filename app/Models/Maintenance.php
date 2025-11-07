<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Import SoftDeletes

class Maintenance extends Model
{
    use HasFactory, SoftDeletes; // Use SoftDeletes

    protected $fillable = [
        'tenant_id',
        'category',
        'urgency',
        'description',
        'photo',
        'status',
        'scheduled_date', // Added
        'notes',          // Added
    ];

    /**
     * Get the tenant that submitted the request.
     */
    public function tenant()
    {
        // Assumes your Tenant model is in App\Models\Tenant
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id');
    }
}