<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\VatCustomer;
use App\Models\File;    


class BusinessInfo extends Model
{
    //
    use HasFactory;
    protected $fillable = ['user_id','name','phone','vat_customer_id','address','terms','image_id'];

    public function user() { return $this->belongsTo(User::class); }
    public function vatCustomer() { return $this->belongsTo(VatCustomer::class); }
    public function image() { return $this->belongsTo(File::class,'image_id'); }
}

