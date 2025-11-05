<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

final class UserApiTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;

    public function testGetUsers(): void
    {
        $client = self::createClient();
        $client->request('GET', '/api/users');
        self::assertResponseIsSuccessful();
    }
}
