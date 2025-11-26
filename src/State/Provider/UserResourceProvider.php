<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiFilter\QueryFilterTrait;
use App\ApiFilter\UserFilterInput;
use App\ApiResource\UserResource;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @implements ProviderInterface<UserResource>
 */
readonly class UserResourceProvider implements ProviderInterface
{
    use QueryFilterTrait;

    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @return UserResource|UserResource[]|null
     */
    public function provide(
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ): UserResource|array|null {
        if ($operation instanceof CollectionOperationInterface) {
            $request = $context['request'] ?? null;

            $filter = new UserFilterInput();

            if ($request) {
                $email = $request->query->get('email');
                $roles = $request->query->get('roles');

                $filter->email = $email ?: null;
                $filter->roles = $roles ?: null;
            }

            $filters = $context['filters'] ?? [];

            $page = max(1, (int) ($filters['page'] ?? 1));
            $perPage = (int) ($filters['itemsPerPage'] ?? 25);
            $perPage = min(100, max(1, $perPage));

            $qb = $this->entityManager->createQueryBuilder()
                ->select('u')
                ->from(User::class, 'u');

            $this->applyFilters($qb, $filter);

            if (isset($filters['order'])) {
                foreach ($filters['order'] as $field => $direction) {
                    $qb->addOrderBy('u.' . $field, $direction);
                }
            }

            $qb->setMaxResults($perPage)
                ->setFirstResult(($page - 1) * $perPage);

            $users = $qb->getQuery()->getResult();

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
