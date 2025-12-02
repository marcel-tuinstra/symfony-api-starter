<?php

declare(strict_types=1);

namespace App\Tests\Unit\Api\Input;

use App\Api\Input\UserInput;
use App\Enum\User\Role;
use App\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserInputTest extends UnitTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    #[DataProvider('provideInvalidInputs')]
    public function testInvalidInputFailsValidation(callable $factory, string $expectedMessageContains): void
    {
        // Arrange
        $input = $factory();

        // Act
        $violations = $this->validator->validate($input, null, ['user:create']);

        // Assert
        $this->assertGreaterThan(0, $violations->count());
        $this->assertStringContainsString($expectedMessageContains, $violations[0]->getMessage());
    }

    public function testValidInputPassesValidation(): void
    {
        // Arrange
        $input = new UserInput();
        $input->email = 'user@example.com';
        $input->roles = [Role::ADMIN->value, Role::USER->value];

        // Act
        $violations = $this->validator->validate($input, null, ['user:create']);

        // Assert
        $this->assertCount(0, $violations);
    }

    /**
     * @return iterable<string, array{callable:callable():UserInput, string}>
     */
    public static function provideInvalidInputs(): iterable
    {
        yield 'missing email' => [
            fn (): UserInput => (function (): UserInput {
                $input = new UserInput();
                $input->roles = [Role::USER->value];
                return $input;
            })(),
            'This value should not be blank.',
        ];

        yield 'invalid role' => [
            fn (): UserInput => (function (): UserInput {
                $input = new UserInput();
                $input->email = 'user@example.com';
                $input->roles = ['INVALID_ROLE'];
                return $input;
            })(),
            'not a valid choice',
        ];
    }
}
