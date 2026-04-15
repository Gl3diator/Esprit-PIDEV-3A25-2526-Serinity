<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Access\AuditLog;
use App\Entity\Access\AuthSession;
use App\Enum\AuditAction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class AuditLogService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack,
        private readonly TokenGenerator $tokenGenerator,
    ) {
    }


    public function log(AuthSession $session, AuditAction $action): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $userAgent = (string) ($request?->headers->get('User-Agent') ?? '');

        $audit = (new AuditLog())
            ->setId($this->tokenGenerator->generateUuidV4())
            ->setAction($action->value)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setPrivateIpAddress((string) ($request?->getClientIp() ?? '127.0.0.1'))
            ->setHostname($request?->getHost())
            ->setOsName($this->extractOsName($userAgent))
            ->setAuthSession($session);

        $this->entityManager->persist($audit);
    }

    private function extractOsName(string $userAgent): ?string
    {
        if ($userAgent === '') {
            return null;
        }

        return match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Macintosh') => 'macOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad') => 'iOS',
            default => 'Unknown',
        };
    }
}
