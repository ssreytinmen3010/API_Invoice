<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_path',
        'file_type'
    ];

    public function getFileUrl()
    {
        return Storage::url($this->file_path);
    }
}
