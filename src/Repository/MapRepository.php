<?php

namespace Betreuteszocken\CsConfig\Repository;

use Betreuteszocken\CsConfig\Entity\Map;
use Doctrine\ORM\EntityRepository;

/**
 * Repository MapRepository
 *
 * @package Betreuteszocken\CsConfig
 */
class MapRepository extends EntityRepository
{
    /**
     * Returns either all removed maps (do not pass any parameter) or returns all existing maps (pass `false`).
     *
     * @param bool $removed `false` to return all existing maps, `true` to return all removed maps, default is `false`
     *
     * @return Map[]
     */
    public function findAllByRemovedFlag(bool $removed = true): array
    {
        $queryBuilder = $this
            ->getEntityManager()->createQueryBuilder()
            ->select('map')
            ->from(Map::class, 'map')
            ->orderBy('map.name', 'ASC')
        ;

        if ($removed) {
            $queryBuilder->where('map.removed IS NOT NULL');
        } else {
            $queryBuilder->where('map.removed IS NULL');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Returns all BZ default maps which are not removed.
     *
     * @param bool $onlyNonRemoved
     *
     * @return Map[]
     */
    public function findAllDefault($onlyNonRemoved = true): array
    {
        $queryBuilder = $this
            ->getEntityManager()->createQueryBuilder()
            ->select('map')
            ->where('map.default IS NOT NULL')
            ->from(Map::class, 'map')
            ->orderBy('map.name', 'ASC')
        ;

        if ($onlyNonRemoved) {
            $queryBuilder->andWhere('map.removed IS NULL');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Returns all BZ origin maps which are not removed.
     *
     * @return Map[]
     */
    public function findAllOrigin(): array
    {
        $queryBuilder = $this
            ->getEntityManager()->createQueryBuilder()
            ->select('map')
            ->from(Map::class, 'map')
            ->Where('map.removed IS NULL')
            ->andWhere('map.origin IS NOT NULL')
            ->orderBy('map.name', 'ASC')
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}