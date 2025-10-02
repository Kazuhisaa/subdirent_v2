<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Unit;

class Application extends Model
{
    //
  
    protected $fillable =[

        'first_name',
        'middle_name',
        'last_name',
        'email',
        'contact_num',
        'unit_id'
    ];
    

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
