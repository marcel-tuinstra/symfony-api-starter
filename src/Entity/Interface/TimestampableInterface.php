<?php

namespace App\Entity\Interface;

use DateTimeImmutable;
use DateTimeInterface;

interface TimestampableInterface
{
    public function getCreatedAt(): DateTimeImmutable;

    public function getUpdatedAt(): ?DateTimeInterface;

    public function getDeletedAt(): ?DateTimeImmutable;

    public function isDeleted(): bool;

    public function softDelete(): void;
}
