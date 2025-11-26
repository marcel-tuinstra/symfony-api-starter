<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Interface\TimestampableInterface;

/**
 * @template T of TimestampableInterface
 * @template TContext of array
 * @implements ProcessorInterface<T, TContext>
 *
 * Handles soft deletion of entities that implement {@see TimestampableInterface}.
 * Marks the entity as deleted (using `softDelete()`) and persists it.
 */
readonly class SoftDeleteProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<T, TContext> $persistProcessor
     */
    public function __construct(
        private ProcessorInterface $persistProcessor,
    ) {
    }

    /**
     * @return T|T[]|null
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data->softDelete();

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
