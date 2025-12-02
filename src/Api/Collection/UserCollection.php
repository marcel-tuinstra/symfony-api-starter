<?php

namespace App\Api\Collection;

use App\Api\Contract\TimestampedResourceInterface;
use App\Api\Contract\TimestampedResourceTrait;
use App\Entity\User;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;

#[Map(source: User::class)]
final class UserCollection implements TimestampedResourceInterface
{
    use TimestampedResourceTrait;

    public Uuid $id;

    public string $email;

    public array $roles = [];
}
