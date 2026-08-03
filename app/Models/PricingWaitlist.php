<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingWaitlist extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'status',
        'notes',
    ];
}
