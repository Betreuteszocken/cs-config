<?php

namespace Betreuteszocken\CsConfig\Service;

use Betreuteszocken\CsConfig\Entity\Cycle;
use Betreuteszocken\CsConfig\Entity\CycleConfig;
use Betreuteszocken\CsConfig\Entity\Map;
use Betreuteszocken\CsConfig\Entity\MapCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CycleGenerator
 *
 * @package Betreuteszocken\CsConfig
 */
class CycleGenerator
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager = null;

    /**
     * @var MapService
     */
    protected $mapService = null;

    /**
     * MapService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param MapService $mapService
     */
    public function __construct(EntityManagerInterface $entityManager, MapService $mapService)
    {
        mt_srand();
        $this->entityManager = $entityManager;
        $this->mapService    = $mapService;
    }

    /**
     * @param CycleConfig $cycleConfig
     * @param OutputInterface|null $output
     *
     * @return Cycle
     */
    public function create(CycleConfig $cycleConfig, ?OutputInterface $output = null): Cycle
    {
        $cycle = Cycle::create()->setConfig($cycleConfig);

        $output = $output ?? (new NullOutput());

        $output->draw = 1;

        $this
            ->addMamiMaps($cycle, $output)
            ->addUserMaps($cycle, $output)
            ->addDefaultMaps($cycle, $output)
            ->addDefaultCategoryMaps($cycle, $output)
            ->addOriginMaps($cycle, $output)
            ->addRandomMaps($cycle, $output);

        return $cycle;
    }

    /**
     * @param Cycle $cycle
     * @param OutputInterface $output
     *
     * @return CycleGenerator
     */
    protected function addMamiMaps(Cycle $cycle, OutputInterface $output): CycleGenerator
    {
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf("Try to add %d mami map(s)", $cycle->getConfig()->getMamiMaps()->count()));
        }

        foreach ($cycle->getConfig()->getMamiMaps() as $_map) {

//            if (!is_null($_map->getRemoved())) {
//                // warning
//            }

            if (!$cycle->containsMap($_map)) {
                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                    $output->writeln(sprintf(' %3s: %s', sprintf('#%d', $output->draw), $_map->getName()));
                }
                $cycle->addMamiMap($_map);
            }

            $output->draw++;
        }

        return $this;
    }

    /**
     * @param Cycle $cycle
     * @param OutputInterface $output
     *
     * @return CycleGenerator
     */
    protected function addUserMaps(Cycle $cycle, OutputInterface $output): CycleGenerator
    {
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf("Try to add %d forum maps:", $cycle->getConfig()->getUserMaps()));
        }

        /** @var Map[] $availableUserMaps */
        $availableUserMaps = $this->mapService->getCountedMaps();

        $total = array_reduce($availableUserMaps, function (int $_carry, Map $_map) {
            return $_carry + $_map->getCount();
        }, 0);

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $output->writeln(sprintf('  > Shuffle %d user maps (%d counts)', count($availableUserMaps), $total));
        }

        shuffle($availableUserMaps);

        // 0 < 2 ? 20 > 0 ?
        while ($cycle->getUserMaps()->count() < $cycle->getConfig()->getUserMaps() && count($availableUserMaps) > 0) {

            $_total = array_reduce($availableUserMaps, function (int $_carry, Map $_map) {
                return $_carry + $_map->getCount();
            }, 0);

            $_originRandom = mt_rand(1, $_total);

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln(sprintf('  > Draw a random number [%d-%d]: %d', 1, $_total, $_originRandom));
            }

            $_random = $_originRandom;

            foreach ($availableUserMaps as $_index => $_map) {

                if ($_random <= $_map->getCount()) {

                    /** @var Map[] $_extracted_map */
                    $_extracted_map = array_splice($availableUserMaps, $_index, 1);

                    $_map = array_pop($_extracted_map);

                    if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                        $output->writeln(sprintf('  > Get counted map at index %d: %s / %d count(s)', $_originRandom, $_map->getName(), $_map->getCount()));
                        $output->writeln(sprintf('  > Remove map %s from current forum map set', $_map->getName()));
                    }

                    if (!$cycle->containsMap($_map)) {

                        $cycle->addUserMap($_map);

                        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                                $output->writeln('  > Map is not in the current cycle -> add map');
                            }
                            $output->writeln(sprintf(' %3s: %s', sprintf('#%d', $output->draw), $_map->getName()));
                        }
                        $output->draw++;
                    } elseif ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                        $output->writeln('  > Map already in cycle -> skip map');
                    }

                    break;
                }

                $_random = $_random - $_map->getCount();
            }
        }

        if ($cycle->getUserMaps()->count() < $cycle->getConfig()->getUserMaps() && count($availableUserMaps) === 0) {
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln('  > No more user maps available to pick a map.');
            }
        }

        return $this;
    }

    /**
     *
     * @param Cycle $cycle
     * @param OutputInterface $output
     *
     * @return CycleGenerator
     */
    protected function addDefaultMaps(Cycle $cycle, OutputInterface $output): CycleGenerator
    {
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf("Try to add %d default bz maps:", $cycle->getConfig()->getDefaultMaps()));
        }

        $availableDefaultMaps = $this->entityManager->getRepository(Map::class)->findAllDefault();

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $output->writeln(sprintf('  > Shuffle %d default bz maps', count($availableDefaultMaps)));
        }

        shuffle($availableDefaultMaps);

        // 0 < 2 ? 20 > 0 ?
        while ($cycle->getDefaultMaps()->count() < $cycle->getConfig()->getDefaultMaps() && count($availableDefaultMaps) > 0) {

            $_map = array_pop($availableDefaultMaps);

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln(sprintf('  > Draw and remove first map from mixed default maps set: %s', $_map->getName()));
            }

            if (!$cycle->containsMap($_map)) {

                $cycle->addDefaultMap($_map);

                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                    if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                        $output->writeln('  > Map is not in the current cycle -> add map');
                    }
                    $output->writeln(sprintf(' %3s: %s', sprintf('#%d', $output->draw), $_map->getName()));
                }
                $output->draw++;
            } elseif ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln('  > Map already in cycle -> skip map');
            }
        }

        if ($cycle->getDefaultMaps()->count() < $cycle->getConfig()->getDefaultMaps() && count($availableDefaultMaps) === 0) {
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln('  > No more bz default maps available to pick a map.');
            }
        }

        return $this;
    }


    /**
     *
     * @param Cycle $cycle
     * @param OutputInterface $output
     *
     * @return CycleGenerator
     */
    protected function addDefaultCategoryMaps(Cycle $cycle, OutputInterface $output): CycleGenerator
    {
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf("Try to add %d maps from all bz default categories:", $cycle->getConfig()->getDefaultCategoryMaps()));
        }

        $defaultMapCategories = $this->entityManager->getRepository(MapCategory::class)->findAllDefault();

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $output->writeln('  > Adding the maps of all bz categories together');
        }

        $availableServerCategoryMaps = array_reduce($defaultMapCategories, function (array $_maps, MapCategory $_mapCategory) {
            return array_merge($_maps, $_mapCategory->getMaps());
        }, array());

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $output->writeln(sprintf('  > Shuffle the %d maps of all default bz categories', count($availableServerCategoryMaps)));
        }

        shuffle($availableServerCategoryMaps);

        // 0 < 2 ? 20 > 0 ?
        while ($cycle->getDefaultCategoryMaps()->count() < $cycle->getConfig()->getDefaultCategoryMaps() && count($availableServerCategoryMaps) > 0) {

            $_map = array_pop($availableServerCategoryMaps);

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln(sprintf('  > Draw and remove first map from mixed map set: %s', $_map->getName()));
            }

            if (!$cycle->containsMap($_map)) {

                $cycle->addDefaultCategoryMap($_map);

                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                    if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                        $output->writeln('  > Map is not in the current cycle -> add map');
                    }
                    $output->writeln(sprintf(' %3s: %s', sprintf('#%d', $output->draw), $_map->getName()));
                }
                $output->draw++;

            } elseif ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln('  > Map already in cycle -> skip map');
            }
        }

        if ($cycle->getDefaultCategoryMaps()->count() < $cycle->getConfig()->getDefaultCategoryMaps() && count($availableServerCategoryMaps) === 0) {
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln('  > No more maps from bz default categories available to pick a map.');
            }
        }

        return $this;
    }

    /**
     *
     * @param Cycle $cycle
     * @param OutputInterface $output
     *
     * @return CycleGenerator
     */
    protected function addOriginMaps(Cycle $cycle, OutputInterface $output): CycleGenerator
    {
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf("Try to add %d origin CS1.6 maps:", $cycle->getConfig()->getOriginMaps()));
        }

        $availableOriginMaps = $this->entityManager->getRepository(Map::class)->findAllOrigin();

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $output->writeln(sprintf('  > Shuffle all %d origin CS1.6 maps', count($availableOriginMaps)));
        }

        shuffle($availableOriginMaps);

        // 0 < 2 ? 20 > 0 ?
        while ($cycle->getOriginMaps()->count() < $cycle->getConfig()->getOriginMaps() && count($availableOriginMaps) > 0) {

            $_map = array_pop($availableOriginMaps);

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln(sprintf('  > Draw and remove first map from mixed map set: %s', $_map->getName()));
            }

            if (!$cycle->containsMap($_map)) {

                $cycle->addOriginMap($_map);

                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                    if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                        $output->writeln('  > Map is not in the current cycle -> add map');
                    }
                    $output->writeln(sprintf(' %3s: %s', sprintf('#%d', $output->draw), $_map->getName()));
                }
                $output->draw++;
            } elseif ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln('  > Map already in cycle -> skip map');
            }
        }

        if ($cycle->getOriginMaps()->count() < $cycle->getConfig()->getOriginMaps() && count($availableOriginMaps) === 0) {
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln('  > No more origin CS1.6 maps available to pick a map.');
            }
        }

        return $this;
    }

    /**
     *
     * @param Cycle $cycle
     * @param OutputInterface $output
     *
     * @return CycleGenerator
     */
    protected function addRandomMaps(Cycle $cycle, OutputInterface $output): CycleGenerator
    {
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln(sprintf("Try to add %d random maps:", $cycle->getConfig()->getRandomMaps()));
        }

        $availableMaps = $this->entityManager->getRepository(Map::class)->findAllByRemovedFlag(false);

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $output->writeln(sprintf('  > Shuffle all %d random maps', count($availableMaps)));
        }

        shuffle($availableMaps);

        // 0 < 2 ? 20 > 0 ?
        while ($cycle->getRandomMaps()->count() < $cycle->getConfig()->getRandomMaps() && count($availableMaps) > 0) {

            $_map = array_pop($availableMaps);

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln(sprintf('  > Draw and remove first map from mixed map set: %s', $_map->getName()));
            }

            if (!$cycle->containsMap($_map)) {

                $cycle->addRandomMap($_map);

                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                    if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                        $output->writeln('  > Map is not in the current cycle -> add map');
                    }
                    $output->writeln(sprintf(' %3s: %s', sprintf('#%d', $output->draw), $_map->getName()));
                }
                $output->draw++;
            } elseif ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln('  > Map already in cycle -> skip map');
            }
        }

        if ($cycle->getRandomMaps()->count() < $cycle->getConfig()->getRandomMaps() && count($availableMaps) === 0) {
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $output->writeln('  > No more random maps available to pick a map.');
            }
        }

        return $this;
    }

}