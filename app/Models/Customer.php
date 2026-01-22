<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VatCustomer;

class Customer extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'alternative_phone',
        'vat_customer_id',
        'address',
        'note',
    ];

    public function vatCustomer()
    {
        return $this->belongsTo(VatCustomer::class, 'vat_customer_id');
    }
}
