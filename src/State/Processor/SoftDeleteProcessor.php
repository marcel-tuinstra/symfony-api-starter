<?php

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Contract\TimestampedResourceInterface;
use App\Entity\Interface\TimestampableInterface;
use InvalidArgumentException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

readonly class SoftDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ObjectMapperInterface $objectMapper
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array|object|null
    {
        if (! $data instanceof TimestampableInterface) {
            throw new InvalidArgumentException('SoftDeleteProcessor expects a TimestampableInterface instance.');
        }

        $data->softDelete();

        $entity = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        if (! is_object($entity)) {
            throw new InvalidArgumentException('SoftDeleteProcessor expects an object from the persist processor.');
        }

        $targetClass = $operation->getClass() ?? $entity::class;

        $resource = $this->objectMapper->map($entity, $targetClass);

        if (! $resource instanceof TimestampedResourceInterface) {
            throw new InvalidArgumentException(sprintf(
                'SoftDeleteProcessor expected %s, got %s',
                TimestampedResourceInterface::class,
                get_debug_type($resource)
            ));
        }

        return $resource;
    }
}
