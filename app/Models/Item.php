<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\File;


class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'item_code',
        'note',
        'image_id',
    ];


    public function image()
    {
        return $this->belongsTo(File::class, 'image_id');
    }
    //

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'item_id');
    }
}
