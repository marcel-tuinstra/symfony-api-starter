<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Interface\TimestampableInterface;

/**
 * @template T of TimestampableInterface
 * @implements ProcessorInterface<T>
 *
 * Handles soft deletion of entities that implement {@see TimestampableInterface}.
 * Marks the entity as deleted (using `softDelete()`) and persists it.
 */
readonly class SoftDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
    ) {
    }

    /**
     * Marks the given entity as deleted and persists the change.
     *
     * @param TimestampableInterface $data The entity to soft delete.
     * @param Operation $operation The current API Platform operation metadata.
     * @param array<string, mixed> $uriVariables URI variables for the operation.
     * @param array<string, mixed> $context The execution context.
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $data->softDelete();

        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
