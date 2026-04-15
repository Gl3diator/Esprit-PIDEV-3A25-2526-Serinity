<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class ResetPasswordService
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly TokenGenerator $tokenGenerator,
    ) {
    }

    public function issueCode(string $email): string
    {
        $code = $this->tokenGenerator->generateResetCode();

        $this->cache->get($this->key($email), function (ItemInterface $item) use ($code): string {
            $item->expiresAfter(600);

            return $code;
        });

        return $code;
    }

    public function matches(string $email, string $code): bool
    {
        $stored = $this->cache->get($this->key($email), static fn (): ?string => null);

        return is_string($stored) && hash_equals($stored, $code);
    }

    public function clear(string $email): void
    {
        $this->cache->delete($this->key($email));
    }

    private function key(string $email): string
    {
        return 'reset_code_' . hash('sha256', mb_strtolower(trim($email)));
    }
}
