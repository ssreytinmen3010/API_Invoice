<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;

class ExchangeRate extends Model
{
    //
    protected $fillable = [
        'currency',
        'rate',
        'is_active',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'exchange_rate_id');
    }
}
