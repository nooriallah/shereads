<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Interest extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function questionOptions(): BelongsToMany
    {
        return $this->belongsToMany(QuestionOption::class, 'answer_interest')
            ->withPivot('weight')
            ->withTimestamps();
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class)
            ->withPivot('weight')
            ->withTimestamps();
    }
}
