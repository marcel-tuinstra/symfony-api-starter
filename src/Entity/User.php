<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interface\IdentifiableInterface;
use App\Entity\Interface\TimestampableInterface;
use App\Entity\Trait\IdentifiableTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Enum\User\Role;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, IdentifiableInterface, TimestampableInterface
{
    use IdentifiableTrait;
    use TimestampableTrait;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'simple_array')]
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
            throw new InvalidArgumentException('One or more provided roles are invalid.');
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
