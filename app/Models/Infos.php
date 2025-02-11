<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infos extends Model
{
    use HasFactory;

    protected $fillable =[
        'LinkIframeMap',
        'title',
        'email',
        'adresse',
        'phoneNumber',
        'instagram',
        'facebook',
        'twitter',
    ];
}
