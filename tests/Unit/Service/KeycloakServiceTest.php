<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\KeycloakService;
use App\Tests\Unit\UnitTestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class KeycloakServiceTest extends UnitTestCase
{
    public function testItLogsWarningWhenRoleIsMissing(): void
    {
        // Arrange
        $httpClient = $this->mock(HttpClientInterface::class);
        $logger = $this->mock(LoggerInterface::class);

        $tokenResponse = $this->createResponse([
            'access_token' => 'access-token',
        ]);
        $createUserResponse = $this->createResponse(
            statusCode: 201,
            headers: [
                'location' => ['https://keycloak.test/admin/realms/dev/users/uuid'],
            ]
        );
        $roleLookupResponse = $this->createResponse(statusCode: 404);

        $httpClient->expects(self::exactly(3))
            ->method('request')
            ->willReturnCallback(function (string $method, string $url, array $options = []) use (
                $tokenResponse,
                $createUserResponse,
                $roleLookupResponse
            ) {
                static $call = 0;
                $call++;

                return match ($call) {
                    1 => $this->assertTokenRequest($method, $url, $options, $tokenResponse),
                    2 => $this->assertCreateUserRequest($method, $url, $options, $createUserResponse),
                    3 => $this->assertRoleLookupRequest($method, $url, $options, $roleLookupResponse),
                    default => $this->fail('Unexpected HTTP client call.'),
                };
            });

        $logger->expects(self::once())
            ->method('warning')
            ->with('Role not found in Keycloak', [
                'role' => 'ROLE_USER',
            ]);
        $logger->expects(self::never())->method('error');

        $service = new KeycloakService(
            $httpClient,
            $logger,
            'https://keycloak.test',
            'dev',
            'client-id',
            'client-secret'
        );

        // Act
        $service->createUserInKeycloak('jane@example.com', ['ROLE_USER']);

        // Assert
        // Assertions are captured via mock expectations and request verifications.
    }

    public function testItCreatesUserAndAssignsRoles(): void
    {
        // Arrange
        $httpClient = $this->mock(HttpClientInterface::class);
        $logger = $this->mock(LoggerInterface::class);

        $tokenResponse = $this->createResponse([
            'access_token' => 'access-token',
        ]);
        $createUserResponse = $this->createResponse(
            statusCode: 201,
            headers: [
                'location' => ['https://keycloak.test/admin/realms/dev/users/uuid'],
            ]
        );
        $roleLookupResponse = $this->createResponse([
            'id' => 'role-id',
            'name' => 'ROLE_ADMIN',
        ]);
        $assignRoleResponse = $this->createResponse(statusCode: 204);

        $httpClient->method('request')
            ->willReturnCallback(function (string $method, string $url, array $options = []) use (
                $tokenResponse,
                $createUserResponse,
                $roleLookupResponse,
                $assignRoleResponse
            ) {
                static $call = 0;
                $call++;

                return match ($call) {
                    1 => $this->assertTokenRequest($method, $url, $options, $tokenResponse),
                    2 => $this->assertCreateUserRequest($method, $url, $options, $createUserResponse),
                    3 => $this->assertRoleLookupRequest($method, $url, $options, $roleLookupResponse, 'ROLE_ADMIN'),
                    4 => $this->assertAssignRoleRequest($method, $url, $options, $assignRoleResponse),
                    default => $assignRoleResponse,
                };
            });

        $logger->expects(self::never())->method('warning');

        $service = new KeycloakService(
            $httpClient,
            $logger,
            'https://keycloak.test',
            'dev',
            'client-id',
            'client-secret'
        );

        // Act
        $service->createUserInKeycloak('admin@example.com', ['ROLE_ADMIN']);

        // Assert
        // Assertions are captured via mock expectations.
    }

    public function testItReusesExistingUserWhenConflict(): void
    {
        // Arrange
        $httpClient = $this->mock(HttpClientInterface::class);
        $logger = $this->mock(LoggerInterface::class);

        $tokenResponse = $this->createResponse([
            'access_token' => 'access-token',
        ]);
        $conflictResponse = $this->createResponse(statusCode: 409);
        $findUserResponse = $this->createResponse([
            [
                'id' => 'existing-id',
            ],
        ]);
        $roleLookupResponse = $this->createResponse([
            'id' => 'role-id',
            'name' => 'ROLE_USER',
        ]);
        $assignRoleResponse = $this->createResponse(statusCode: 204);

        $httpClient->method('request')
            ->willReturnCallback(function (string $method, string $url, array $options = []) use (
                $tokenResponse,
                $conflictResponse,
                $findUserResponse,
                $roleLookupResponse,
                $assignRoleResponse
            ) {
                static $call = 0;
                $call++;

                return match ($call) {
                    1 => $this->assertTokenRequest($method, $url, $options, $tokenResponse),
                    2 => $this->assertCreateUserRequest($method, $url, $options, $conflictResponse, 'user@example.com'),
                    3 => $this->assertFindUserRequest($method, $url, $options, $findUserResponse),
                    4 => $this->assertRoleLookupRequest($method, $url, $options, $roleLookupResponse, 'ROLE_USER'),
                    5 => $this->assertAssignRoleRequest($method, $url, $options, $assignRoleResponse, 'existing-id', 'ROLE_USER'),
                    default => $assignRoleResponse,
                };
            });

        $logger->expects(self::never())->method('warning');
        $logger->expects(self::never())->method('error');

        $service = new KeycloakService(
            $httpClient,
            $logger,
            'https://keycloak.test',
            'dev',
            'client-id',
            'client-secret'
        );

        // Act
        $service->createUserInKeycloak('user@example.com', ['ROLE_USER']);
    }

    private function assertTokenRequest(string $method, string $url, array $options, ResponseInterface $response): ResponseInterface
    {
        $this->assertSame('POST', $method);
        $this->assertSame('https://keycloak.test/realms/dev/protocol/openid-connect/token', $url);
        $this->assertSame([
            'client_id' => 'client-id',
            'client_secret' => 'client-secret',
            'grant_type' => 'client_credentials',
        ], $options['body'] ?? []);

        return $response;
    }

    private function assertCreateUserRequest(string $method, string $url, array $options, ResponseInterface $response, string $email = 'jane@example.com'): ResponseInterface
    {
        $this->assertSame('POST', $method);
        $this->assertSame('https://keycloak.test/admin/realms/dev/users', $url);
        $this->assertSame([
            'Authorization' => 'Bearer access-token',
            'Content-Type' => 'application/json',
        ], $options['headers'] ?? []);
        $this->assertSame([
            'enabled' => true,
            'email' => $email,
            'username' => $email,
        ], $options['json'] ?? []);

        return $response;
    }

    private function assertRoleLookupRequest(string $method, string $url, array $options, ResponseInterface $response, string $role = 'ROLE_USER'): ResponseInterface
    {
        $this->assertSame('GET', $method);
        $this->assertSame(sprintf('https://keycloak.test/admin/realms/dev/roles/%s', $role), $url);
        $this->assertSame([
            'Authorization' => 'Bearer access-token',
        ], $options['headers'] ?? []);

        return $response;
    }

    private function assertAssignRoleRequest(string $method, string $url, array $options, ResponseInterface $response, string $userId = 'uuid', string $role = 'ROLE_ADMIN'): ResponseInterface
    {
        $this->assertSame('POST', $method);
        $this->assertSame(sprintf('https://keycloak.test/admin/realms/dev/users/%s/role-mappings/realm', $userId), $url);
        $this->assertSame([
            'Authorization' => 'Bearer access-token',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], $options['headers'] ?? []);

        $payload = $options['json'][0] ?? [];
        $this->assertSame('role-id', $payload['id'] ?? null);
        $this->assertSame($role, $payload['name'] ?? null);

        return $response;
    }

    private function assertFindUserRequest(string $method, string $url, array $options, ResponseInterface $response): ResponseInterface
    {
        $this->assertSame('GET', $method);
        $this->assertSame('https://keycloak.test/admin/realms/dev/users?email=user%40example.com', $url);
        $this->assertSame([
            'Authorization' => 'Bearer access-token',
        ], $options['headers'] ?? []);

        return $response;
    }

    private function createResponse(array $payload = [], int $statusCode = 200, array $headers = []): ResponseInterface
    {
        $response = $this->mock(ResponseInterface::class);

        $response->method('toArray')->willReturn($payload);
        $response->method('getStatusCode')->willReturn($statusCode);
        $response->method('getHeaders')->willReturn($headers);
        $response->method('getContent')->willReturn(json_encode($payload, JSON_THROW_ON_ERROR));

        return $response;
    }
}
