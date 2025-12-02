<?php

declare(strict_types=1);

namespace App\Api\Contract;

use DateTimeImmutable;
use DateTimeInterface;

trait TimestampedResourceTrait
{
    public ?DateTimeImmutable $createdAt = null;

    public ?DateTimeInterface $updatedAt = null;

    public ?DateTimeImmutable $deletedAt = null;

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }
}
