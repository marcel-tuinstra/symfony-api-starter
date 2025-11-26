<?php

declare(strict_types=1);

namespace App\ApiFilter;

use Symfony\Component\Validator\Constraints as Assert;

final class UserFilterInput
{
    #[Assert\Email(message: 'Invalid email')]
    public ?string $email = null;

    /** @var string[]|string|null */
    #[Assert\Choice(choices: ['ROLE_USER', 'ROLE_ADMIN'], multiple: false)]
    public array|string|null $roles = null;
}
