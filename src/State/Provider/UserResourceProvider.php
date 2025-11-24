<?php

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\UserResource;
use App\Entity\User;
use App\Repository\UserRepository;

readonly class UserResourceProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): UserResource|array|null
    {
        // Collection: /api/users
        if ($operation instanceof CollectionOperationInterface) {
            $filters = $context['filters'] ?? [];

            $criteria = [];
            $order = [];

            // SEARCH
            if (isset($filters['email'])) {
                $criteria['email'] = $filters['email'];
            }

            // ORDER
            if (isset($filters['order'])) {
                $order = $filters['order'];
            }

            // PAGINATION
            $page = $filters['page'] ?? 1;
            $perPage = $filters['itemsPerPage'] ?? 10;

            $users = $this->userRepository->findBy(
                $criteria,
                $order,
                $perPage,
                ($page - 1) * $perPage
            );

            return array_map([UserResource::class, 'fromEntity'], $users);
        }

        $id = $uriVariables['id'] ?? null;
        if ($id === null) {
            return null;
        }

        /** @var User|null $user */
        $user = $this->userRepository->findOneByUuid($id);
        if (! $user) {
            return null;
        }

        return UserResource::fromEntity($user);
    }
}
