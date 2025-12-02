<?php

declare(strict_types=1);

namespace App\Tests\Functional\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TestAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $users,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization', '');
        $token = preg_replace('/^Bearer\s+/i', '', $authHeader) ?? '';

        if ($token === '') {
            throw new AuthenticationException('Missing Authorization header');
        }

        $email = 'tester@example.com';
        $rolesString = 'ROLE_USER';

        [$parsedEmail, $parsedRolesString] = array_pad(explode('|', $token, 2), 2, null);
        $email = $parsedEmail ?: $email;
        $rolesString = $parsedRolesString ?? $rolesString;

        $roles = array_values(array_filter(array_map('trim', explode(',', $rolesString))));

        $userLoader = function (string $identifier) use ($roles): UserInterface {
            $user = $this->users->findOneBy([
                'email' => $identifier,
            ]) ?? new User($identifier);
            $user->setRoles($roles === [] ? ['ROLE_USER'] : $roles);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $user;
        };

        return new SelfValidatingPassport(new UserBadge($email, $userLoader));
    }

    public function onAuthenticationSuccess(Request $request, \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'message' => 'Authentication failed',
            'error' => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new JsonResponse([
            'message' => 'Authentication required',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
