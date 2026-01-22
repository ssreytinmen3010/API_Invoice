<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\ExchangeRate;
use App\Models\DeliveryFee;
use App\Models\File;    
use App\Models\InvoiceItem;

class Invoice extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'invoice_date',
        'customer_id',
        'exchange_rate_id',
        'image_id',
        'discount_id',
        'delivery_fee_id',
        'vat_percent',
        'note',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }
    public function exchangeRate()
    {
        return $this->belongsTo(ExchangeRate::class, 'exchange_rate_id');
    }
    public function deliveryFee()
    {
        return $this->belongsTo(DeliveryFee::class, 'delivery_fee_id');
    }
    public function image()
    {
        return $this->belongsTo(File::class, 'image_id');
    }
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

} 