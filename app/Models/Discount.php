<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;

class Discount extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'percent',
        'amount',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'discount_id');
    }
}
