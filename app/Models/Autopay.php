<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Autopay extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'autopays';

    protected $fillable = [
        'tenant_id',
        'contract_id',
        'stripe_customer_id',
        'stripe_subscription_id',
        'stripe_payment_method_id',
        'amount',
        'start_date',
        'next_due_date',
        'status',
        'last_invoice_id',
        'last_payment_intent',
        'remarks',
    ];

    /**
     * Relationships
     */

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Helpers
     */

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isPaused()
    {
        return $this->status === 'paused';
    }

    public function isCanceled()
    {
        return $this->status === 'canceled';
    }
}
