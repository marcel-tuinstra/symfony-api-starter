<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Enum\User\Role;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
    ],
    normalizationContext: [
        'groups' => ['user:read'],
    ],
    denormalizationContext: [
        'groups' => ['user:write'],
    ]
)]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private UuidInterface $id;

    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [Role::USER->value];

    public function __construct(string $email)
    {
        $this->id = Uuid::uuid4();
        $this->email = $email;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}
