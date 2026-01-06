<?php

namespace App\Enums;

enum UserRules: string
{
    case ADMIN = 'admin';
    case SUBSCRIBER = 'subscriber';
    case EDITOR = 'editor';
    case VISITOR = 'visitor';

    // Get all the values as an associative array (name and value) for a select field.
    public static function toSelectArray(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
   

}