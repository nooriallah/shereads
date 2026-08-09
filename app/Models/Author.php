<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
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

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'deathdate' => 'date',
        ];
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class)->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->lastname);
    }
}
