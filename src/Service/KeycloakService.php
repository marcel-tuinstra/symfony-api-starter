<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class KeycloakService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $keycloakBaseUrl,
        private string $keycloakRealm,
        private string $keycloakClientId,
        private string $keycloakClientSecret
    ) {
    }

    /**
     * Create user in Keycloak + assign roles (realm roles).
     *
     * @param string[] $roles
     */
    public function createUserInKeycloak(string $email, array $roles): void
    {
        try {
            $token = $this->getAdminAccessToken();

            // 1) Try creating user
            $createUserResponse = $this->httpClient->request('POST', sprintf(
                '%s/admin/realms/%s/users',
                $this->keycloakBaseUrl,
                $this->keycloakRealm
            ), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'enabled' => true,
                    'email' => $email,
                    'username' => $email,
                ],
            ]);

            $status = $createUserResponse->getStatusCode();

            if ($status === 201) {
                // Extract user ID from Location header
                $locationHeader = $createUserResponse->getHeaders()['location'][0] ?? null;
                $userId = $locationHeader ? basename($locationHeader) : null;
            } elseif ($status === 409) {
                // User already exists: fetch user ID
                $userId = $this->findUserIdByEmail($token, $email);
            } else {
                $this->logger->error('Failed to create user in Keycloak', [
                    'email' => $email,
                    'response' => $createUserResponse->getContent(false),
                ]);
                return;
            }

            if (! $userId) {
                $this->logger->error('Could not determine Keycloak user ID', [
                    'email' => $email,
                ]);
                return;
            }

            // 2) Assign roles
            foreach ($roles as $roleName) {
                $this->assignRole($token, $userId, $roleName);
            }
        } catch (\Throwable $e) {
            $this->logger->error('Keycloak sync failed: ' . $e->getMessage(), [
                'email' => $email,
                'roles' => $roles,
            ]);
        }
    }

    /**
     * Assign a ROLE_* realm role to a user.
     */
    private function assignRole(string $token, string $userId, string $roleName): void
    {
        $role = $this->getRoleRepresentation($token, $roleName);

        if (! $role) {
            $this->logger->warning('Role not found in Keycloak', [
                'role' => $roleName,
            ]);
            return;
        }

        $url = sprintf(
            '%s/admin/realms/%s/users/%s/role-mappings/realm',
            $this->keycloakBaseUrl,
            $this->keycloakRealm,
            $userId
        );

        $this->httpClient->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [[
                'id' => $role['id'],
                'name' => $role['name'],
            ]],
        ]);
    }

    /**
     * Fetch full Keycloak role representation (ID + name).
     *
     * @return array<string, mixed>|null
     */
    private function getRoleRepresentation(string $token, string $roleName): ?array
    {
        $url = sprintf(
            '%s/admin/realms/%s/roles/%s',
            $this->keycloakBaseUrl,
            $this->keycloakRealm,
            $roleName
        );

        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        return $response->toArray(false);
    }

    /**
     * Find a Keycloak user by email and return the UUID.
     */
    private function findUserIdByEmail(string $token, string $email): ?string
    {
        $response = $this->httpClient->request('GET', sprintf(
            '%s/admin/realms/%s/users?email=%s',
            $this->keycloakBaseUrl,
            $this->keycloakRealm,
            urlencode($email)
        ), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $users = $response->toArray(false);

        return $users[0]['id'] ?? null;
    }

    /**
     * Get admin token using client_credentials flow
     */
    private function getAdminAccessToken(): string
    {
        $response = $this->httpClient->request('POST', sprintf(
            '%s/realms/%s/protocol/openid-connect/token',
            $this->keycloakBaseUrl,
            $this->keycloakRealm
        ), [
            'body' => [
                'client_id' => $this->keycloakClientId,
                'client_secret' => $this->keycloakClientSecret,
                'grant_type' => 'client_credentials',
            ],
        ]);

        $data = $response->toArray(false);

        return $data['access_token'] ?? '';
    }
}
