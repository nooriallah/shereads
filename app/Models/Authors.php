<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Authors extends Model
{
    protected $fillable = [
        'name',
        'lastname',
        'website',
        'author_photo',
        'country',
        'birthdate',
        'deathdate',
        'bio',
    ];
}
