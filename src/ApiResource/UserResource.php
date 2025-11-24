<?php

namespace App\ApiResource;

use App\Entity\User;
use Symfony\Component\Serializer\Attribute\Groups;

class UserResource
{
    public function __construct(
        #[Groups(['user:read'])]
        public string $id,
        #[Groups(['user:read'])]
        public string $email,
        #[Groups(['user:read'])]
        public array $roles,
        #[Groups(['user:read'])]
        public array $meta
    ) {
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            id: $user->getId(),
            email: $user->getEmail(),
            roles: $user->getRoles(),
            meta: [
                'createdAt' => $user->getCreatedAt(),
                'updatedAt' => $user->getUpdatedAt(),
                'deletedAt' => $user->getDeletedAt(),
            ]
        );
    }
}
