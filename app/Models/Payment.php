<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'contract_id',
        'amount',
        'payment_method', 
        'payment_status', 
        'payment_date',
        'reference_no',   
        'invoice_no',    
        'invoice_pdf',    
        'remarks',        
        'unit_id',        
    ];

    // Relationships
    public function tenant() {
        return $this->belongsTo(Tenant::class);
    }

    public function unit() {
        return $this->belongsTo(Unit::class);
    }

    public function contract() {
        return $this->belongsTo(Contract::class);
    }


}