<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'tenant_id',
        'unit_id',
        'contract_start',
        'contract_end',
        'contract_duration',
        'total_price',
        'downpayment',
        'monthly_payment',
        'unit_price',
        'payment_due_date',
        'status',
        'remarks',
        
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    } 
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    
}
