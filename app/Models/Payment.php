<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\File;

class Payment extends Model
{
    //use HasFactory;
    protected $fillable = ['payment_info','bank_image_id'];

    public function bankImage() { return $this->belongsTo(File::class,'bank_image_id'); }
}
