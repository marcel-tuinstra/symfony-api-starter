<?php

declare(strict_types=1);

namespace App\Factory;

use InvalidArgumentException;

final readonly class FactoryRegistry
{
    /**
     * @param iterable<FactoryInterface> $factories
     */
    public function __construct(
        private iterable $factories
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(string $type, array $data = []): object
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($type)) {
                return $factory->create($data);
            }
        }

        throw new InvalidArgumentException(sprintf('No factory supports type "%s"', $type));
    }
}
