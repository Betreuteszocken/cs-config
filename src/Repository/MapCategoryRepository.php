<?php

namespace Betreuteszocken\CsConfig\Repository;

use Betreuteszocken\CsConfig\Entity\Map;
use Betreuteszocken\CsConfig\Entity\MapCategory;
use Doctrine\ORM\EntityRepository;

/**
 * Repository MapCategoryRepository
 *
 * @package Betreuteszocken\CsConfig
 */
class MapCategoryRepository extends EntityRepository
{
    /**
     * @return MapCategory[]
     */
    public function findAll(): array
    {
        /** @var MapCategory[] $mapCategories */
        $mapCategories = $this->findBy([], ['name' => 'ASC']);

        return $this->addMaps($mapCategories);
    }


    /**
     * @return MapCategory[]
     */
    public function findAllDefault(): array
    {
        $queryBuilder = $this
            ->createQueryBuilder('map_category')
            ->andWhere('map_category.default IS NOT NULL')
            ->orderBy('map_category.name', 'ASC')
        ;

        $mapCategories = $queryBuilder->getQuery()->getResult();

        return $this->addMaps($mapCategories, false);
    }

    /**
     * @param MapCategory[] $mapCategories
     * @param bool          $addNonCategorizedMaps `true` to add an category (without a name) for all maps
     *                                             which do not fit in any of the passes categories,
     *                                             `false` to ignore non-matching maps
     *
     * @return MapCategory[]
     */
    protected function addMaps(array $mapCategories, bool $addNonCategorizedMaps = true): array
    {
        $maps = $this->getEntityManager()->getRepository(Map::class)->findAllByRemovedFlag(false);

        foreach ($mapCategories as $_mapCategory) {

            $_maps = array();

            $maps = array_filter($maps, function (Map $_map) use ($_mapCategory, &$_maps) {

                if (preg_match($_mapCategory->getRegex(), $_map->getName()) !== 1) {
                    return true;
                };
                array_push($_maps, $_map);
                return false;
            });

            $_mapCategory->setMaps($_maps);
        }

        if ($addNonCategorizedMaps && !empty($maps)) {
            array_push($mapCategories, MapCategory::create()->setMaps($maps));
        }

        return $mapCategories;
    }
}