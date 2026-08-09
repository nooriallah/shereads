<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case WRITER = 'writer';
    case SUBSCRIBER = 'subscriber';

    /**
     * All role values, e.g. for validation rules or select fields.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    /**
     * Human-readable label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::EDITOR => 'Editor',
            self::WRITER => 'Writer',
            self::SUBSCRIBER => 'Subscriber',
        };
    }

    /**
     * Roles that may access the admin dashboard.
     *
     * @return array<int, string>
     */
    public static function adminRoles(): array
    {
        return [self::SUPER_ADMIN->value, self::ADMIN->value];
    }
}
