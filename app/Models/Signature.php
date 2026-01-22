<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\File;

class Signature extends Model
{
    // use HasFactory;
    protected $fillable = ['image_id'];

    public function image() { return $this->belongsTo(File::class,'image_id'); }
}
