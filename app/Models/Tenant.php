<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'contact_num',
        'unit_id',
    ];

    // Relationship: Tenant belongs to Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
     public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
        public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function contract()
{
    return $this->hasOne(Contract::class, 'tenant_id');
}
public function autopay()
{
    return $this->hasOne(Autopay::class)->latestOfMany();
}



}
    
    



