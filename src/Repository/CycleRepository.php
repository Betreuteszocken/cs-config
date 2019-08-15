<?php

namespace Betreuteszocken\CsConfig\Repository;

use Betreuteszocken\CsConfig\Entity\Cycle;
use Doctrine\ORM\EntityRepository;

/**
 * Repository CycleRepository
 *
 * @package Betreuteszocken\CsConfig
 */
class CycleRepository extends EntityRepository
{
    /**
     * {@inheritDoc}
     *
     * @return Cycle[]
     */
    public function findAll(): array
    {
        $queryBuilder = $this
            ->createQueryBuilder('cycle')
            ->innerJoin('cycle.config', 'config')
            ->orderBy('cycle.id', 'DESC')
        ;

        $cycles = $queryBuilder->getQuery()->getResult();

        // get the cycle with highest id
        /** @var Cycle $maxCycle */
        $maxCycle = array_reduce($cycles, function (?Cycle $_maxCycle, Cycle $_currentCycle) {
            if (is_null($_maxCycle)) {
                return $_currentCycle;
            }
            return ($_maxCycle->getId() > $_currentCycle->getId()) ? $_maxCycle : $_currentCycle;
        });

        // and set this cycle as the current running cycle
        if (!is_null($maxCycle)) {
            $maxCycle->setCurrent(true);
        }

        return $cycles;
    }

    /**
     * Returns the current installed cycle.
     *
     * @return Cycle|null
     */
    public function findCurrent(): ?Cycle
    {
        $queryBuilder = $this
            ->createQueryBuilder('cycle')
            ->orderBy('cycle.id', 'DESC')
            ->setMaxResults(1)
        ;

        /** @var Cycle[] $cycles */
        $cycles = $queryBuilder->getQuery()->getResult();

        return empty($cycles) ? null : $cycles[0]->setCurrent(true);
    }
}