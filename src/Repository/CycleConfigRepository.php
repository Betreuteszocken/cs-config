<?php

namespace Betreuteszocken\CsConfig\Repository;

use Betreuteszocken\CsConfig\Entity\CycleConfig;
use Doctrine\ORM\EntityRepository;

/**
 * Repository CycleConfigRepository
 *
 * @package Betreuteszocken\CsConfig
 */
class CycleConfigRepository extends EntityRepository
{
    /**
     * Returns either all removed maps (do not pass any parameter) or returns all existing maps (pass `false`).
     *
     * @return CycleConfig|null
     */
    public function findCurrent(): ?CycleConfig
    {
        $queryBuilder = $this
            ->createQueryBuilder('cycle_config')
            ->leftJoin('cycle_config.mamiMaps', 'mamiMaps')
            ->orderBy('cycle_config.id', 'DESC')
            ->setMaxResults(1)
        ;

        $cycles = $queryBuilder->getQuery()->getResult();

        return empty($cycles) ? null : $cycles[0];
    }
}