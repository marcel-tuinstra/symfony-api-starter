<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class KeycloakAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly HttpClientInterface    $httpClient,
        private readonly EntityManagerInterface $em,
        private readonly UserRepository         $users,
        private readonly string                 $introspectionUrl,
        private readonly string                 $clientId,
        private readonly string                 $clientSecret,
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        $auth = $request->headers->get('Authorization');

        return $auth && str_starts_with($auth, 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s+(.*)/i', $authHeader, $m)) {
            throw new AuthenticationException('No bearer token found');
        }

        $token = $m[1];

        // Call Keycloak introspection endpoint
        $response = $this->httpClient->request('POST', $this->introspectionUrl, [
            'body' => [
                'token'         => $token,
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
            ],
        ]);

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new AuthenticationException('Introspection request failed');
        }

        $data = $response->toArray(false);

        if (empty($data['active'])) {
            throw new AuthenticationException('Token is not active');
        }

        // Use email as identifier (as you requested)
        $email = $data['email'] ?? $data['preferred_username'] ?? null;

        if (!$email) {
            throw new AuthenticationException('No email claim in token');
        }

        $kcRoles = $data['realm_access']['roles'] ?? [];
        if (!is_array($kcRoles)) {
            $kcRoles = [];
        }

        // Map Keycloak roles → Symfony roles
        $roles = array_values(array_unique(array_filter(
            $kcRoles,
            static fn(string $role): bool => str_starts_with($role, 'ROLE_')
        )));

        // Every authenticated user gets at least ROLE_USER
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        $userLoader = function (string $identifier) use ($roles): UserInterface {
            $user = $this->users->findOneBy(['email' => $identifier]);

            if (!$user) {
                $user = new User($identifier);
                $this->em->persist($user);
            }

            $user->setRoles($roles);
            $this->em->flush();

            return $user;
        };

        return new SelfValidatingPassport(new UserBadge($email, $userLoader));
    }

    public function onAuthenticationSuccess(Request $request, \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, string $firewallName): ?Response
    {
        // let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'message' => 'Authentication failed',
            'error'   => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }
}
