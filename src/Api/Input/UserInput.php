<?php

declare(strict_types=1);

namespace App\Api\Input;

use App\Entity\User;
use App\Enum\User\Role;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: User::class)]
final class UserInput
{
    private const array ROLE_CHOICES = [Role::USER->value, Role::ADMIN->value];

    #[Assert\NotBlank(groups: ['user:create'])]
    #[Assert\Email(groups: ['user:create', 'user:update'])]
    #[Map(target: 'email', if: [self::class, 'isNotNull'])]
    public ?string $email = null;

    /**
     * @var string[]|null
     */
    #[Assert\All([
        new Assert\Choice(choices: self::ROLE_CHOICES, groups: ['user:create', 'user:update']),
    ])]
    #[Map(target: 'roles', if: [self::class, 'isNotNull'])]
    public ?array $roles = null;

    /**
     * @param null|bool|int|float|string|array<array-key, mixed>|object $value
     */
    public static function isNotNull(null|bool|int|float|string|array|object $value): bool
    {
        return $value !== null;
    }
}
