<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // ✅ Add this

class Admin extends Authenticatable
{
    use HasFactory, HasApiTokens; // ✅ Include HasApiTokens

    protected $table = 'admins';

    protected $fillable = [
        'fullname',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}