<?php

declare(strict_types=1);

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

final class RegisterRequest
{
    // TEMP DEV MODE: keep only the minimum validation needed to create a user.
    #[Assert\NotBlank(message: 'Email is required.')]
    #[Assert\Email(message: 'Please provide a valid email address.')]
    public string $email = '';

    // TEMP DEV MODE: password strength policy intentionally disabled.
    #[Assert\NotBlank(message: 'Password is required.')]
    public string $password = '';

    #[Assert\Choice(
        choices: ['THERAPIST', 'PATIENT'],
        message: 'Role must be either THERAPIST or PATIENT.',
    )]
    public string $role = 'PATIENT';
}
