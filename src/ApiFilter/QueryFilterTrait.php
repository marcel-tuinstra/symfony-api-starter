<?php

namespace App\ApiFilter;

use Doctrine\ORM\QueryBuilder;

trait QueryFilterTrait
{
    protected function applyFilters(QueryBuilder $qb, UserFilterInput $input): void
    {
        if ($input->email) {
            $this->applyEmailFilter($qb, $input->email);
        }

        $role = is_array($input->roles) ? ($input->roles[0] ?? null) : $input->roles;
        if ($role) {
            $this->applyRoleFilter($qb, $role);
        }
    }

    protected function applyEmailFilter(QueryBuilder $queryBuilder, string $email): void
    {
        $queryBuilder->andWhere('LOWER(u.email) LIKE LOWER(:email)')
            ->setParameter('email', "%{$email}%");
    }

    protected function applyRoleFilter(QueryBuilder $queryBuilder, string $role): void
    {
        $queryBuilder->andWhere('CONCAT(u.roles, \'\') LIKE :role')
            ->setParameter('role', '%"' . $role . '"%');
    }
}
