<?php

namespace Betreuteszocken\CsConfig\Repository;

use Betreuteszocken\CsConfig\Entity\Log;
use Doctrine\ORM\EntityRepository;

/**
 * Repository LogRepository
 *
 * @package Betreuteszocken\CsConfig
 */
class LogRepository extends EntityRepository
{
    /**
     *
     * @param int $limit
     *
     * @return Log[]
     */
    public function findLastLogs(int $limit = 100): array
    {
        $queryBuilder = $this
            ->getEntityManager()->createQueryBuilder()
            ->select('log')
            ->from(Log::class, 'log')
            ->setMaxResults($limit)
            ->orderBy('log.createdAt', 'DESC')
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}