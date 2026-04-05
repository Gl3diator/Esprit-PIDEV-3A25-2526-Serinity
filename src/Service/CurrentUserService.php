<?php

namespace App\Service;

use App\Model\CurrentUser;
use Doctrine\DBAL\Connection;

class CurrentUserService
{
    //2a69ad69-302a-11f1-88f1-3ddd9b736dc8
    public const FALLBACK_USER_ID = '3';

    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function requireUser(): CurrentUser
    {
        $fallback = $this->connection->fetchAssociative(
            'SELECT user_id, username, roles FROM profiles WHERE user_id = :id LIMIT 1',
            ['id' => self::FALLBACK_USER_ID]
        );

        if (!is_array($fallback)) {
            throw new \RuntimeException(sprintf('Fallback user id "%s" was not found in profiles.', self::FALLBACK_USER_ID));
        }

        $roleLabel = strtolower((string) ($fallback['roles'] ?? 'client'));

        return new CurrentUser(
            (string) ($fallback['user_id'] ?? self::FALLBACK_USER_ID),
            (string) ($fallback['username'] ?? 'Unknown User'),
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
