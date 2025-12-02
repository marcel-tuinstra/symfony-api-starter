<?php

declare(strict_types=1);

namespace App\Tests\Unit\Util;

use App\Tests\Unit\UnitTestCase;
use App\Util\DateTimeUtil;
use DateTime;
use DateTimeImmutable;

class DateTimeUtilTest extends UnitTestCase
{
    public function testItProvidesUtcDateTimes(): void
    {
        // Arrange

        // Act
        $mutable = DateTimeUtil::nowUtc();
        $immutable = DateTimeUtil::nowUtcAsImmutable();

        // Assert
        $this->assertInstanceOf(DateTime::class, $mutable);
        $this->assertInstanceOf(DateTimeImmutable::class, $immutable);
        $this->assertSame('UTC', $mutable->getTimezone()->getName());
        $this->assertSame('UTC', $immutable->getTimezone()->getName());
    }
}
