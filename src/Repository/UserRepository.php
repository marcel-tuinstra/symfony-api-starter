<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByUuid(Uuid $uuid): ?User
    {
        $queryBuilder = $this->createQueryBuilder('u');
        $queryBuilder
            ->where('u.id = :id')
            ->setParameter('id', $uuid, UuidType::NAME);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
