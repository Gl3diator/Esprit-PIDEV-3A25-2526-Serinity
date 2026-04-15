<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Repository\Access\AuditLogRepository;
use App\Repository\Access\AuthSessionRepository;
use App\Repository\Access\UserRepository;

final class DashboardService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AuthSessionRepository $authSessionRepository,
        private readonly AuditLogRepository $auditLogRepository,
    ) {
    }

    public function getStatistics(): array
    {
        return [
            'totalUsers' => $this->userRepository->countUsers(),
            'activeSessions' => $this->authSessionRepository->countActiveSessions(),
            'recentAuditEvents' => $this->auditLogRepository->countRecentEvents(days: 7),
            'profileCompletionPercentage' => $this->userRepository->getProfileCompletionPercentage(),
        ];
    }

    /**
     * @return array<int, array{timestamp: \DateTimeImmutable, eventType: string, userEmail: string, ipAddress: string}>
     */
    public function getRecentActivity(int $limit = 10): array
    {
        $logs = $this->auditLogRepository->findRecent($limit);
        $activity = [];

        foreach ($logs as $log) {
            $session = $log->getAuthSession();
            $activity[] = [
                'timestamp' => $log->getCreatedAt(),
                'eventType' => $log->getAction(),
                'userEmail' => $session->getUser()->getEmail(),
                'ipAddress' => $log->getPrivateIpAddress(),
            ];
        }

        return $activity;
    }
}