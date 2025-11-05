<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Unit;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;
  
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'contact_num',
        'unit_id',
        'status',
        'downpayment',
        'monthly_payment',
        'unit_price',
        'payment_due_date',
        'contract_years',
        'remarks',
        'contract_start'
        
    ];

    protected $dates = ['deleted_at'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}