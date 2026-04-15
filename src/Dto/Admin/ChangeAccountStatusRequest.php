<?php

declare(strict_types=1);

namespace App\Dto\Admin;

use Symfony\Component\Validator\Constraints as Assert;

final class ChangeAccountStatusRequest // ✅ readonly supprimé
{
    public function __construct(
        #[Assert\NotBlank(message: 'Account status is required')]
        #[Assert\Choice(choices: ['ACTIVE', 'DISABLED'], message: 'Invalid account status. Must be ACTIVE or DISABLED')]
        public readonly string $accountStatus, // ✅ readonly garde sur la propriété (PHP 8.1 OK)
    ) {
    }
}