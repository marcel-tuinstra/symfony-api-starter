<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class UnitTestCase extends TestCase
{
    /**
     * @template T of object
     * @param class-string<T> $className
     * @return MockObject&T
     */
    protected function mock(string $className): MockObject
    {
        /** @var MockObject&T $mock */
        $mock = $this->createMock($className);

        return $mock;
    }

    /**
     * @template T of object
     * @param class-string<T> $className
     * @param array<string, array|bool|float|int|string|null> $configuration
     * @return MockObject&T
     */
    protected function configuredMock(string $className, array $configuration): MockObject
    {
        /** @var MockObject&T $mock */
        $mock = $this->createConfiguredMock($className, $configuration);

        return $mock;
    }
}
