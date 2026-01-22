<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  

    use HasApiTokens, Notifiable;

    protected $fillable = ['phone'];

    protected $hidden = ['remember_token'];

    public function getAuthIdentifierName()
    {
        return 'phone';
    }

    // public function businessInfo()
    // {
    //     return $this->hasOne(BusinessInfo::class);
    // }
}
