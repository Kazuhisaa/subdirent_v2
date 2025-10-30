<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'contract_id',
        'amount',
        'payment_method',
        'payment_status',
        'payment_date',
        'for_month', 
        'reference_no',
        'invoice_no',
        'invoice_pdf',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'payment_date' => 'datetime',
        'for_month' => 'date',
    ];

    // ... (iyong relationships)
    public function tenant() {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class);
    }

    public function contract() {
        return $this->belongsTo(Contract::class);
        
    }
}