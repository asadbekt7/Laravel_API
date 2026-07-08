<?php

namespace App\Auth;

final class MyUwedClaims
{
    /**
     * @param  list<string>  $permissions
     */
    public function __construct(
        public readonly string $staffId,
        public readonly string $fullName,
        public readonly string $email,
        public readonly array $permissions,
        public readonly string $token,
    ) {}

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }
}
