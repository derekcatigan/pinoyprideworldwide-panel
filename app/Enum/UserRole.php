<?php

namespace App\Enum;

enum UserRole: string
{
    case Admin = 'admin';
    case BranchAdmin = 'branch_admin';
    case Agent = 'agent';

    public function displayName(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::BranchAdmin => 'Branch Admin',
            self::Agent => 'Agent',
        };
    }

    public static function options(): array
    {
        return array_map(fn(self $role) => [
            'name' => $role->displayName(),
            'value' => $role->value,
        ], self::cases());
    }
}
