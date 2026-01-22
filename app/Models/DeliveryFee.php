<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;

class DeliveryFee extends Model
{
       use HasFactory;

       protected $fillable = [
           'amount',
       ];

       public function invoices()
       {
           return $this->hasMany(Invoice::class, 'delivery_fee_id');
       }
}
