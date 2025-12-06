<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\KeycloakAuthenticator;
use App\Tests\Unit\UnitTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class KeycloakAuthenticatorTest extends UnitTestCase
{
    public function testAuthenticateCreatesAndUpdatesUserFromToken(): void
    {
        // Arrange
        $httpClient = $this->mock(HttpClientInterface::class);
        $em = $this->mock(EntityManagerInterface::class);
        $repo = $this->mock(UserRepository::class);

        $response = $this->mock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $response->method('toArray')->willReturn([
            'active' => true,
            'email' => 'kc@example.com',
            'realm_access' => [
                'roles' => ['ROLE_ADMIN'],
            ],
        ]);

        $httpClient->method('request')->willReturn($response);
        $repo->method('findOneBy')->willReturn(null);

        $em->expects(self::once())
            ->method('persist')
            ->with(self::callback(fn (User $user): bool => $user->getEmail() === 'kc@example.com'));
        $em->expects(self::once())->method('flush');

        $authenticator = new KeycloakAuthenticator(
            $httpClient,
            $em,
            $repo,
            'https://kc/introspect',
            'client',
            'secret'
        );

        $request = new Request(server: [
            'HTTP_AUTHORIZATION' => 'Bearer token',
        ]);

        // Act
        $passport = $authenticator->authenticate($request);

        // Assert
        $userBadge = $passport->getBadge(\Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge::class);
        $loadedUser = $userBadge->getUser();

        $this->assertSame('kc@example.com', $loadedUser->getUserIdentifier());
        $this->assertContains('ROLE_ADMIN', $loadedUser->getRoles());
        $this->assertContains('ROLE_USER', $loadedUser->getRoles());
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideInvalidTokens')]
    public function testAuthenticateFailsOnInvalidToken(array $tokenPayload, string $expectedExceptionMessage): void
    {
        // Arrange
        $httpClient = $this->mock(HttpClientInterface::class);
        $em = $this->mock(EntityManagerInterface::class);
        $repo = $this->mock(UserRepository::class);

        $response = $this->mock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $response->method('toArray')->willReturn($tokenPayload);

        $httpClient->method('request')->willReturn($response);

        $authenticator = new KeycloakAuthenticator(
            $httpClient,
            $em,
            $repo,
            'https://kc/introspect',
            'client',
            'secret'
        );

        $request = new Request(server: [
            'HTTP_AUTHORIZATION' => 'Bearer token',
        ]);

        // Assert
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        // Act
        $authenticator->authenticate($request);
    }

    /**
     * @return iterable<string, array{array{active?: bool, email?: string, preferred_username?: string, realm_access?: array{roles?: array<int, string>}}, string}>
     */
    public static function provideInvalidTokens(): iterable
    {
        yield 'inactive token' => [
            [
                'active' => false,
            ],
            'Token is not active',
        ];

        yield 'missing email' => [
            [
                'active' => true,
                'realm_access' => [
                    'roles' => [],
                ],
            ],
            'No email claim in token',
        ];
    }
}
