<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Dto\Admin\ChangeAccountStatusRequest;
use App\Dto\Admin\UpdateUserRequest;
use App\Dto\Admin\UserFilterRequest;
use App\Dto\Common\ServiceResult;
use App\Entity\Access\AuthSession;
use App\Entity\Access\User;
use App\Enum\AccountStatus;
use App\Enum\AuditAction;
use App\Enum\UserRole;
use App\Repository\Access\AuthSessionRepository;
use App\Repository\Access\UserRepository;
use App\Service\AuditLogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserManagementService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AuthSessionRepository $authSessionRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly AuditLogService $auditLogService,
        private readonly Security $security,
    ) {
    }

    public function getUsersPaginated(UserFilterRequest $request): array
    {
        return $this->userRepository->findPaginated(
            $request->page,
            $request->limit,
            $request->toFilters()
        );
    }

    public function updateUser(string $id, UpdateUserRequest $request): ServiceResult
    {
        $user = $this->userRepository->find($id);
        if ($user === null) {
            return ServiceResult::failure('User not found.');
        }

        if ($user->getEmail() !== $request->email) {
            $existingUser = $this->userRepository->findByEmail($request->email);
            if ($existingUser !== null && $existingUser->getId() !== $id) {
                return ServiceResult::failure('Email is already in use by another user.');
            }
            $user->setEmail($request->email);
        }

        $role = UserRole::tryFrom($request->role);
        if ($role === null) {
            return ServiceResult::failure('Invalid role.');
        }
        $previousRole = $user->getRole();
        $user->setRole($role->value);

        if ($request->accountStatus !== null) {
            $status = AccountStatus::tryFrom($request->accountStatus);
            if ($status === null) {
                return ServiceResult::failure('Invalid account status.');
            }
            $user->setAccountStatus($status->value);
        }

        if ($request->password !== null) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $request->password);
            $user->setPassword($hashedPassword);
        }

        $user->setUpdatedAt(new \DateTimeImmutable());
        $this->logAdminAction(AuditAction::USER_UPDATED);
        if ($previousRole !== $role->value) {
            $this->logAdminAction(AuditAction::ROLE_CHANGED);
        }
        $this->entityManager->flush();

        return ServiceResult::success('User updated successfully.', ['user' => $user]);
    }

    public function deleteUser(string $id): ServiceResult
    {
        $user = $this->userRepository->find($id);
        if ($user === null) {
            return ServiceResult::failure('User not found.');
        }

        $this->entityManager->remove($user);
        $this->logAdminAction(AuditAction::USER_DELETED);
        $this->entityManager->flush();

        return ServiceResult::success('User deleted successfully.');
    }

    public function changeAccountStatus(string $id, ChangeAccountStatusRequest $request): ServiceResult
    {
        $user = $this->userRepository->find($id);
        if ($user === null) {
            return ServiceResult::failure('User not found.');
        }

        $status = AccountStatus::tryFrom($request->accountStatus);
        if ($status === null) {
            return ServiceResult::failure('Invalid account status.');
        }

        $user->setAccountStatus($status->value);
        $user->setUpdatedAt(new \DateTimeImmutable());
        $this->logAdminAction(AuditAction::USER_UPDATED);
        $this->entityManager->flush();

        return ServiceResult::success('Account status updated successfully.', ['user' => $user]);
    }

    public function getUserStatistics(): array
    {
        return [
            'total'      => $this->userRepository->countUsers(),
            'active'     => $this->userRepository->countByAccountStatus(AccountStatus::ACTIVE),
            'disabled'   => $this->userRepository->countByAccountStatus(AccountStatus::DISABLED),
            'admins'     => $this->userRepository->countByRole(UserRole::ADMIN),
            'therapists' => $this->userRepository->countByRole(UserRole::THERAPIST),
            'patients'   => $this->userRepository->countByRole(UserRole::PATIENT),
        ];
    }

    private function logAdminAction(AuditAction $action): void
    {
        $adminUser = $this->security->getUser();
        if (!$adminUser instanceof User) {
            return;
        }

        $activeSessions = $this->authSessionRepository->findActiveForUser($adminUser);
        if ($activeSessions === []) {
            return;
        }

        /** @var AuthSession $session */
        $session = $activeSessions[0];
        $this->auditLogService->log($session, $action);
    }
}