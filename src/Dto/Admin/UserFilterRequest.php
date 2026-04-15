<?php

declare(strict_types=1);

namespace App\Dto\Admin;

use Symfony\Component\Validator\Constraints as Assert;

final class UserFilterRequest // ✅ readonly retiré de la classe
{
    public function __construct(
        #[Assert\Positive]
        public readonly int $page = 1,

        #[Assert\Positive]
        #[Assert\LessThanOrEqual(100)]
        public readonly int $limit = 20,

        #[Assert\Email(message: 'Invalid email format')]
        public readonly ?string $email = null,

        #[Assert\Choice(choices: ['ADMIN', 'THERAPIST', 'PATIENT'], message: 'Invalid role')]
        public readonly ?string $role = null,

        #[Assert\Choice(choices: ['ACTIVE', 'DISABLED'], message: 'Invalid account status')]
        public readonly ?string $accountStatus = null,
    ) {
    }

    public function toFilters(): array
    {
        $filters = [];

        if ($this->email !== null) {
            $filters['email'] = $this->email;
        }

        if ($this->role !== null) {
            $filters['role'] = $this->role;
        }

        if ($this->accountStatus !== null) {
            $filters['accountStatus'] = $this->accountStatus;
        }

        return $filters;
    }
}