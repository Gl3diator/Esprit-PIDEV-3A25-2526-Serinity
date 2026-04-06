<?php

namespace App\Service;

use App\Model\CurrentUser;

class CurrentUserService
{
    public const FALLBACK_USER_ID = '6affa2df-dda9-442d-99ee-d2a3c1e78c64';
    public const FALLBACK_USERNAME = 'Serinity User';
    public const FALLBACK_ROLE = 'client';

    public function requireUser(): CurrentUser
    {
        $roleLabel = self::FALLBACK_ROLE;

        return new CurrentUser(
            self::FALLBACK_USER_ID,
            self::FALLBACK_USERNAME,
            $roleLabel,
            $this->normalizeRoles($roleLabel),
        );
    }

    public function isAdmin(?CurrentUser $user = null): bool
    {
        $targetUser = $user ?? $this->requireUser();

        if (strtolower($targetUser->getRoleLabel()) === 'admin') {
            return true;
        }

        return in_array('ROLE_ADMIN', $targetUser->getRoles(), true);
    }

    /**
     * @return list<string>
     */
    private function normalizeRoles(string $roleLabel): array
    {
        if ($roleLabel === 'admin' || $roleLabel === 'role_admin') {
            return ['ROLE_ADMIN', 'ROLE_USER'];
        }

        return ['ROLE_USER'];
    }
}
