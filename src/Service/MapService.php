<?php

namespace Betreuteszocken\CsConfig\Service;

use Betreuteszocken\CsConfig\ForumApiBundle\Service\ForumMapInterface;
use Betreuteszocken\CsConfig\Entity\Map;
use Betreuteszocken\CsConfig\Repository\MapRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class MapService
 *
 * @package Betreuteszocken\CsConfig
 */
class MapService
{
    /**
     * @var MapRepository
     */
    protected $repository = null;

    /**
     * @var ForumMapInterface
     */
    protected $forumMapService = null;

    /**
     * MapService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ForumMapInterface $forumMapService
     */
    public function __construct(EntityManagerInterface $entityManager, ForumMapInterface $forumMapService)
    {
        $this->repository      = $entityManager->getRepository(Map::class);
        $this->forumMapService = $forumMapService;
    }

    /**
     * @param bool $orderedByCount `true` to return maps ordered by count, else `false`
     *
     * @return Map[]
     */
    public function getCountedMaps($orderedByCount = false): array
    {
        $maps = $this->repository->findAllByRemovedFlag(false);

        $counts = $this->forumMapService->getMapCounts();

        $countedMaps = array_filter($maps, function (Map $_map) use ($counts) {
            if (in_array($_map->getName(), array_keys($counts))) {
                $_map->setCount($counts[$_map->getName()]);
                return true;
            }
            return false;
        });

        if (!$orderedByCount) {
            return $countedMaps;
        }

        usort($countedMaps, function (Map $_mapA, Map $_mapB) {
            if ($_mapA->getCount() === $_mapB->getCount()) {
                return strcasecmp($_mapA->getName(), $_mapB->getName());
            }

            return $_mapA->getCount() > $_mapB->getCount() ? -1 : 1;
        });

        return $countedMaps;
    }
}