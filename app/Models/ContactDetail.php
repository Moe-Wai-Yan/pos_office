<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'phone',
        'address',
        'facebook_url',
        'messenger_url',
        'viber_url',
        'tiktok_url'
    ];
}
