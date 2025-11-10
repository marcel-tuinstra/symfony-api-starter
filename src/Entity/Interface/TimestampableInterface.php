<?php

namespace App\Entity\Interface;

use DateTime;
use DateTimeImmutable;

interface TimestampableInterface
{
    public function getCreatedAt(): DateTimeImmutable;

    public function getUpdatedAt(): ?DateTime;

    public function getDeletedAt(): ?DateTimeImmutable;

    public function isDeleted(): bool;

    public function softDelete(): void;
}
