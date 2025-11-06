<?php

declare(strict_types=1);

namespace App\Factory;

interface FactoryInterface
{
    public function supports(string $type): bool;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data = []): object;
}
