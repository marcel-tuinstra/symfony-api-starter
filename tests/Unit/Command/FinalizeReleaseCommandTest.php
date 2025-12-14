<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\FinalizeReleaseCommand;
use App\Service\ReleaseManager;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

class FinalizeReleaseCommandTest extends UnitTestCase
{
    private ReleaseManager&MockObject $releaseManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->releaseManager = $this->mock(ReleaseManager::class);
    }

    public function testFinalizeDelegatesToManager(): void
    {
        // Arrange
        $command = new FinalizeReleaseCommand($this->releaseManager);
        $tester = new CommandTester($command);

        $this->releaseManager
            ->expects(self::once())
            ->method('finalizeChangelog')
            ->with('v1.2.3');

        // Act
        $exitCode = $tester->execute([
            '--version' => 'v1.2.3',
        ]);

        // Assert
        self::assertSame(0, $exitCode);
        self::assertStringContainsString('Changelog finalized for v1.2.3', $tester->getDisplay());
    }

    public function testFinalizeFailureReturnsError(): void
    {
        // Arrange
        $command = new FinalizeReleaseCommand($this->releaseManager);
        $tester = new CommandTester($command);

        $this->releaseManager
            ->expects(self::once())
            ->method('finalizeChangelog')
            ->willThrowException(new RuntimeException('fail'));

        // Act
        $exitCode = $tester->execute([
            '--version' => 'v1.2.3',
        ]);

        // Assert
        self::assertSame(1, $exitCode);
        self::assertStringContainsString('fail', $tester->getDisplay());
    }
}
