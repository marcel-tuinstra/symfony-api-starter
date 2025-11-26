<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\ApiResource\UserResource;
use App\Entity\Interface\IdentifiableInterface;
use App\Entity\Interface\TimestampableInterface;
use App\Entity\Trait\IdentifiableTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Enum\User\Role;
use App\Repository\UserRepository;
use App\State\Processor\SoftDeleteProcessor;
use App\State\Provider\UserResourceProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Get(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
        new Delete(security: "is_granted('ROLE_ADMIN')", processor: SoftDeleteProcessor::class),
    ],
    normalizationContext: [
        'groups' => ['user:read'],
    ],
    output: UserResource::class,
    provider: UserResourceProvider::class
)]
#[ApiFilter(SearchFilter::class, properties: [
    'email' => 'partial',
    'roles' => 'partial',
])]
class User implements UserInterface, IdentifiableInterface, TimestampableInterface
{
    use IdentifiableTrait;
    use TimestampableTrait;

    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    /**
     * @var string[]
     */
    #[Groups(['user:read', 'user:write'])]
    #[ORM\Column(type: 'json')]
    private array $roles = [Role::USER->value];

    public function __construct(string $email)
    {
        $this->email = $email;
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
        return array_unique($this->roles);
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
        $validRoles = array_map(fn (Role $role) => $role->value, Role::cases());
        $normalized = array_map(fn (string $r) => strtoupper(trim($r)), $roles);
        $filteredRoles = array_values(array_intersect($normalized, $validRoles));

        if ($filteredRoles === []) {
            throw new \InvalidArgumentException('One or more provided roles are invalid.');
        }

        if (! in_array(Role::USER->value, $filteredRoles, true)) {
            $filteredRoles[] = Role::USER->value;
        }

        $this->roles = $filteredRoles;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return null; // Not needed when using Keycloak
    }

    public function eraseCredentials(): void
    {
        // No sensitive temporary data stored
    }
}
