<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\SecurityScheme;
use ApiPlatform\OpenApi\OpenApi;

final readonly class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $openApiFactory
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->openApiFactory)($context);
        $components = $openApi->getComponents();

        // Add the Bearer security scheme (immutably)
        $securityScheme = new SecurityScheme(
            type: 'http',
            description: 'Authorization header using the Bearer scheme',
            scheme: 'bearer',
            bearerFormat: 'JWT'
        );

        $components = $components->withSecuritySchemes(
            new \ArrayObject([
                'bearerAuth' => $securityScheme,
            ])
        );

        // Re-attach components and require security globally
        $openApi = $openApi
            ->withComponents($components)
            ->withSecurity([[
                'bearerAuth' => [],
            ]]);

        return $openApi;
    }
}
