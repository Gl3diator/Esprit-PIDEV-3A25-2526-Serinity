<?php

namespace App\Service;

class ProfileLookupService
{
    /**
     * @return array{user_id: string, username: string, roles: string}|null
     */
    public function findById(string $userId): ?array
    {
        if ($userId === '') {
            return null;
        }

        $isCurrentUser = $userId === CurrentUserService::FALLBACK_USER_ID;

        return [
            'user_id' => $userId,
            'username' => $isCurrentUser ? CurrentUserService::FALLBACK_USERNAME : sprintf('User %s', $userId),
            'roles' => CurrentUserService::FALLBACK_ROLE,
        ];
    }

    /**
     * @param list<string> $userIds
     *
     * @return array<string, string>
     */
    public function usernamesByIds(array $userIds): array
    {
        $cleanIds = array_values(array_unique(array_filter($userIds, static fn (string $id): bool => $id !== '')));
        if ($cleanIds === []) {
            return [];
        }

        $map = [];
        foreach ($cleanIds as $id) {
            $map[$id] = $id === CurrentUserService::FALLBACK_USER_ID
                ? CurrentUserService::FALLBACK_USERNAME
                : sprintf('User %s', $id);
        }

        return $map;
    }

    public function countProfiles(): int
    {
        return 1;
    }
}
