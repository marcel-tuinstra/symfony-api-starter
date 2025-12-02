<?php

namespace App\Api\Resource;

use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Api\Collection\UserCollection;
use App\Api\Contract\TimestampedResourceInterface;
use App\Api\Contract\TimestampedResourceTrait;
use App\Api\Input\UserInput;
use App\Entity\User;
use App\State\Processor\SoftDeleteProcessor;
use App\State\Provider\ResourceCollectionProvider;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/users/{id}',
            uriVariables: ['id'],
            security: "is_granted('ROLE_USER')",
        ),
        new GetCollection(
            uriTemplate: '/users',
            security: "is_granted('ROLE_USER')",
            output: UserCollection::class,
            provider: ResourceCollectionProvider::class,
            parameters: [
                'email' => new QueryParameter(
                    filter: new PartialSearchFilter(),
                    property: 'email', // The internal prop inside of the Entity/User
                ),
                'role' => new QueryParameter(
                    filter: new PartialSearchFilter(),
                    property: 'roles',
                ),
            ],
        ),
        new Post(
            uriTemplate: '/users',
            security: "is_granted('ROLE_ADMIN')",
            validationContext: [
                'groups' => ['user:create'],
            ],
            input: UserInput::class,
        ),
        new Patch(
            uriTemplate: '/users/{id}',
            uriVariables: ['id'],
            security: "is_granted('ROLE_ADMIN')",
            validationContext: [
                'groups' => ['user:update'],
            ],
            input: UserInput::class,
        ),
        new Delete(
            uriTemplate: '/users/{id}',
            uriVariables: ['id'],
            security: "is_granted('ROLE_ADMIN')",
            processor: SoftDeleteProcessor::class
        ),
    ],
    stateOptions: new Options(entityClass: User::class),
)]
#[Map(source: User::class)]
final class UserResource implements TimestampedResourceInterface
{
    use TimestampedResourceTrait;

    public Uuid $id;

    public string $email;

    public array $roles = [];
}
