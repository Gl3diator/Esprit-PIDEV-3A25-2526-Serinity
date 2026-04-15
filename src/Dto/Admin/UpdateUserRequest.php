<?php

declare(strict_types=1);

namespace App\Dto\Admin;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateUserRequest // ✅ readonly retiré de la classe
{
    public function __construct(
        #[Assert\NotBlank(message: 'Email is required')]
        #[Assert\Email(message: 'Invalid email format')]
        public readonly string $email,

        #[Assert\NotBlank(message: 'Role is required')]
        #[Assert\Choice(choices: ['ADMIN', 'THERAPIST', 'PATIENT'], message: 'Invalid role')]
        public readonly string $role,

        #[Assert\Choice(choices: ['ACTIVE', 'DISABLED'], message: 'Invalid account status')]
        public readonly ?string $accountStatus = null,

        #[Assert\Length(min: 8, minMessage: 'Password must be at least 8 characters')]
        public readonly ?string $password = null,
    ) {
    }
}