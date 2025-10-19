<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'unit_id',
        'contract_id',
        'amount',
        'payment_date',
        'invoice',
        'status',
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
    public function payments()
{
    return $this->hasMany(Payment::class);
}

}
