<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\GenerateChangelogCommand;
use App\Service\ChangelogGenerator;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateChangelogCommandTest extends UnitTestCase
{
    private ChangelogGenerator&MockObject $generator;
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = $this->mock(ChangelogGenerator::class);
        $this->tempDir = sys_get_temp_dir() . '/changelog_cmd_' . uniqid('', true);
        mkdir($this->tempDir, 0777, true);
    }

    public function testWritesGeneratedChangelog(): void
    {
        // Arrange
        $command = new GenerateChangelogCommand($this->generator, $this->tempDir);
        $tester = new CommandTester($command);

        $this->generator
            ->expects(self::once())
            ->method('generate')
            ->with(null, null)
            ->willReturn("content\n");

        // Act
        $exitCode = $tester->execute([]);

        // Assert
        self::assertSame(0, $exitCode);
        self::assertStringContainsString('Changelog written to CHANGELOG.md', $tester->getDisplay());
        self::assertFileExists($this->tempDir . '/CHANGELOG.md');
        self::assertSame("content\n", (string) file_get_contents($this->tempDir . '/CHANGELOG.md'));
    }

    public function testFailureReturnsError(): void
    {
        // Arrange
        $command = new GenerateChangelogCommand($this->generator, $this->tempDir);
        $tester = new CommandTester($command);

        $this->generator
            ->expects(self::once())
            ->method('generate')
            ->willThrowException(new RuntimeException('fail'));

        // Act
        $exitCode = $tester->execute([]);

        // Assert
        self::assertSame(1, $exitCode);
        self::assertStringContainsString('fail', $tester->getDisplay());
    }
}
