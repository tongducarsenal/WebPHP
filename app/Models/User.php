<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'avatar',
        'level',
        'description',
        'company_name',
        'country',
        'street_address',
        'postcode_zip',
        'town_city',
        'phone',
    ];

    protected $hidden = [
        "password",
    ];
}
