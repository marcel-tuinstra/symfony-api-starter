<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Enum\User\Role;
use App\Tests\Fixture\UserTestTrait;

class UserApiTest extends FunctionalTestCase
{
    use UserTestTrait;

    public function testUnauthenticatedRequestIsRejected(): void
    {
        // Arrange
        $client = $this->createApiClient();

        // Act
        $client->request('GET', '/api/users');

        // Assert
        $this->assertResponseStatusCodeSame(401);
    }

    public function testAuthenticatedUserCanListUsers(): void
    {
        // Arrange
        $user = $this->createUser('list@example.com');
        $client = $this->createAuthenticatedClient('viewer@example.com', [Role::USER->value]);

        // Act
        $client->request('GET', '/api/users');

        // Assert
        $this->assertResponseIsSuccessful();
        $data = $client->getResponse()->toArray(false);

        $this->assertStringContainsString($user->getEmail(), json_encode($data, JSON_THROW_ON_ERROR));
    }

    public function testAdminCanCreateUser(): void
    {
        // Arrange
        $client = $this->createAuthenticatedClient('admin@example.com', [Role::ADMIN->value, Role::USER->value]);

        // Act
        $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'new-user@example.com',
                'roles' => [Role::ADMIN->value],
            ],
        ]);

        // Assert
        $this->assertResponseStatusCodeSame(201);
        $payload = $client->getResponse()->toArray(false);
        $this->assertSame('new-user@example.com', $payload['email'] ?? null);
        $this->assertContains(Role::ADMIN->value, $payload['roles'] ?? []);
        $this->assertContains(Role::USER->value, $payload['roles'] ?? []);
    }
}
