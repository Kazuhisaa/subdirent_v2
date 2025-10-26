<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

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
        'last_billed_at',
        'next_due_date',
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

    // 🧮 Automatically compute the next due date
    public function getNextDueAttribute()
    {
        $due = $this->next_due_date 
            ? Carbon::parse($this->next_due_date)
            : Carbon::parse($this->contract_start)->addMonth()->day($this->payment_due_date);

        // If past due, move to next month automatically
        if ($due->isPast()) {
            $due = now()->addMonth()->day($this->payment_due_date);
        }

        return $due;
    }

    // 🧾 Check if this month is already paid
    public function isPaidForMonth($month = null)
    {
        $month = $month ?? now()->format('Y-m');
        return $this->payments()
            ->where('payment_status', 'paid')
            ->whereRaw("DATE_FORMAT(payment_date, '%Y-%m') = ?", [$month])
            ->exists();
    }
}
