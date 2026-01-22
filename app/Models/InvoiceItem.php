<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\Invoice;

class InvoiceItem extends Model
{
    //
     use HasFactory;
     protected $fillable = [
         'invoice_id',
         'item_id',
         'unit_price',
         'quantity',
          'status', 
     ];

     public function item()
     {
         return $this->belongsTo(Item::class);
     }
     public function invoice()
     {
         return $this->belongsTo(Invoice::class);
     }

}
