<?php

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use LogicException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

/**
 * @implements ProviderInterface<object>
 */
final readonly class ResourceCollectionProvider implements ProviderInterface
{
    /**
     * @param ProviderInterface<object> $doctrineProvider
     */
    public function __construct(
        #[Autowire(service: CollectionProvider::class)]
        private ProviderInterface $doctrineProvider,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $targetClass = $operation->getClass();
        $output = $operation->getOutput();

        if (is_object($output) && method_exists($output, 'getClass')) {
            $targetClass = $output->getClass() ?? $targetClass;
        } elseif (is_string($output)) {
            $targetClass = $output;
        } elseif (is_array($output)) {
            $targetClass = $output['class'] ?? $targetClass;
        }

        if (! is_string($targetClass) || $targetClass === '') {
            throw new LogicException('Unable to determine the resource class to map to.');
        }

        if (! class_exists($targetClass)) {
            throw new LogicException(sprintf('Target resource class "%s" does not exist.', $targetClass));
        }

        /** @phpstan-var class-string<object> $targetClass */

        $resources = [];

        /** @var iterable<object> $entities */
        $entities = $this->doctrineProvider->provide($operation, $uriVariables, $context);
        foreach ($entities as $entity) {
            $resources[] = $this->objectMapper->map($entity, $targetClass);
        }

        return $resources;
    }
}
