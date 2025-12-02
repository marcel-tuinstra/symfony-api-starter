<?php

declare(strict_types=1);

namespace App\Api\Contract;

use DateTimeImmutable;
use DateTimeInterface;

interface TimestampedResourceInterface
{
    public function getCreatedAt(): ?DateTimeImmutable;

    public function getUpdatedAt(): ?DateTimeInterface;

    public function getDeletedAt(): ?DateTimeImmutable;
}
