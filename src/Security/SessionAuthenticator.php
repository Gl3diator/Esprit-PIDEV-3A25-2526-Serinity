<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\Access\AuthSessionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

final class SessionAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly AuthSessionRepository $authSessionRepository,
    ) {
    }

    public function supports(Request $request): bool
    {
        $refreshToken = $request->cookies->get('refresh_token');

        return $refreshToken !== null && $refreshToken !== '';
    }

    public function authenticate(Request $request): Passport
    {
        $refreshToken = (string) $request->cookies->get('refresh_token');

        $session = $this->authSessionRepository->findValidByRefreshToken($refreshToken);
        if ($session === null) {
            throw new AuthenticationException('Invalid or expired session.');
        }

        return new SelfValidatingPassport(
            new UserBadge($session->getUser()->getUserIdentifier(), static fn () => $session->getUser()),
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response('Authentication failed.', Response::HTTP_UNAUTHORIZED);
    }
}