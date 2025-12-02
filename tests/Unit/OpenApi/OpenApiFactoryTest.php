<?php

declare(strict_types=1);

namespace App\Tests\Unit\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Components;
use ApiPlatform\OpenApi\Model\Info;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\Model\SecurityScheme;
use ApiPlatform\OpenApi\OpenApi;
use App\OpenApi\OpenApiFactory;
use App\Tests\Unit\UnitTestCase;

class OpenApiFactoryTest extends UnitTestCase
{
    public function testItAddsBearerSecurityScheme(): void
    {
        // Arrange
        $innerFactory = $this->mock(OpenApiFactoryInterface::class);
        $originalOpenApi = new OpenApi(
            new Info('Test', '1.0.0'),
            [],
            new Paths(),
            new Components()
        );

        $innerFactory->expects(self::once())
            ->method('__invoke')
            ->with([])
            ->willReturn($originalOpenApi);

        $factory = new OpenApiFactory($innerFactory);

        // Act
        $openApi = $factory();

        // Assert
        $components = $openApi->getComponents();
        $schemes = $components->getSecuritySchemes();

        $this->assertNotNull($schemes);
        $this->assertTrue($schemes->offsetExists('bearerAuth'));

        $scheme = $schemes['bearerAuth'];
        $this->assertInstanceOf(SecurityScheme::class, $scheme);
        $this->assertSame('http', $scheme->getType());
        $this->assertSame('bearer', $scheme->getScheme());
        $this->assertSame('JWT', $scheme->getBearerFormat());
        $this->assertSame([[
            'bearerAuth' => [],
        ]], $openApi->getSecurity());
    }
}
