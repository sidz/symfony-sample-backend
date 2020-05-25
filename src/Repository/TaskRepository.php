<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;

final class TaskRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param DateTimeImmutable $date
     *
     * @return Task[]
     */
    public function findByUserForToday(User $user, DateTimeImmutable $date): array
    {
        $qb = $this->createQueryBuilder('t');

        $qb
            ->where(
                $qb->expr()->eq('t.user', ':owner'),
                $qb->expr()->gte('t.targetDate', ':minDate'),
                $qb->expr()->lte('t.targetDate', ':maxDate'),
            )
            ->setParameters([
                'owner' => $user,
                'minDate' => $date->setTime(0, 0, 0)->format('Y-m-d H:i:s'),
                'maxDate' => $date->setTime(23, 59, 59)->format('Y-m-d H:i:s'),
            ])
            ->orderBy('t.targetDate');

        return $qb->getQuery()
            ->getResult();
    }

    public function findByUserAndUUid(User $user, string $uuid): ?Task
    {
        return $this->findOneBy([
            'uuid' => $uuid,
            'user' => $user,
        ]);
    }
}
