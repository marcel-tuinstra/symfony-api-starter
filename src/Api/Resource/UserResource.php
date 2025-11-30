<?php

namespace App\Api\Resource;

use ApiPlatform\Doctrine\Orm\Filter\ExactFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Entity\User;
use App\State\Processor\SoftDeleteProcessor;
use DateTimeImmutable;
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
            jsonStream: false,
        ),
        new Post(
            uriTemplate: '/users',
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Patch(
            uriTemplate: '/users/{id}',
            uriVariables: ['id'],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            uriTemplate: '/users/{id}',
            uriVariables: ['id'],
            security: "is_granted('ROLE_ADMIN')",
            processor: SoftDeleteProcessor::class
        )
    ],
    stateOptions: new Options(entityClass: User::class),
    jsonStream: true,
)]
#[Map(source: User::class)]
class UserResource
{
    public Uuid $id;

//    #[Map(source: 'email')]
    public string $email;

    public array $roles = [];

    public ?DateTimeImmutable $createdAt = null;
    public ?DateTimeImmutable $updatedAt = null;
    public ?DateTimeImmutable $deletedAt = null;
}
