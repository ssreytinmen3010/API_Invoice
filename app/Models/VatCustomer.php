<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class VatCustomer extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'percent',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'vat_customer_id');
    }
}
