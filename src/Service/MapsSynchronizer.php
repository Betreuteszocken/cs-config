<?php

namespace Betreuteszocken\CsConfig\Service;

use Betreuteszocken\CsConfig\Entity\Map;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MapsSynchronizer
 *
 * @package Betreuteszocken\CsConfig
 */
class MapsSynchronizer
{

    /**
     * @var string[]
     */
    protected $originMapNames = array();

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager = null;

    /**
     * @var MapFileService
     */
    protected $mapFileService = null;

    /**
     * @var LogService
     */
    protected $logService = null;

    /**
     * MapService constructor.
     *
     * @param ContainerInterface     $container
     * @param EntityManagerInterface $entityManager
     * @param MapFileService         $mapFileService
     * @param LogService             $logService
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager, MapFileService $mapFileService, LogService $logService)
    {
        $this->originMapNames = array_filter(array_map('trim', $container->getParameter('bz.cs_config.origin_maps')));

        $this->entityManager  = $entityManager;
        $this->mapFileService = $mapFileService;
        $this->logService     = $logService;
    }

    /**
     * @param boolean              $dryRun
     * @param OutputInterface|null $output
     *
     * @return Map[] all new maps and maps which have been deleted,
     *               use {@see \Betreuteszocken\CsConfig\Entity\Map::isRemoved()} to detect wether the maps are new or deleted
     */
    public function sync($dryRun = false, ?OutputInterface $output = null): array
    {
        $output = $output ?? (new NullOutput());

        // 1. get installed map from file system

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $output->write('<comment>1. Read all maps from file system...</comment> ');
        }
        /** @var string[] $currentMapFileNames */
        $currentMapFileNames = $this->mapFileService->getMapFileNames();

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->write(sprintf('found %d maps ', count($currentMapFileNames)));
        }

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $output->writeln('<info>done</info>');
        }

        // 2. get installed map from database

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $output->write('<comment>2. Fetch all maps from database...</comment> ');
        }
        /** @var Map[] $dbMaps */
        $dbMaps = $this->entityManager->getRepository(Map::class)->findAll();

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->write(sprintf('got %d maps ', count($dbMaps)));
        }

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $output->writeln('<info>done</info>');
        }

        // 3. calculate new and missing maps
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $output->write('<comment>3. Calculate difference between map sets...</comment>');

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln('');
            }
        }

        $mapsToPersist = array();

        foreach ($dbMaps as $_dbMap) {

            // map was found as file
            if (array_key_exists($_dbMap->getName(), $currentMapFileNames)) {

                // if map is marked as "removed" -> mark map as "installed"
                if ($_dbMap->isRemoved()) {
                    $_dbMap->setRemoved(false);
                    array_push($mapsToPersist, $_dbMap);
                }

                unset($currentMapFileNames[$_dbMap->getName()]);

            } else {

                if (!$_dbMap->isRemoved()) {
                    // map is not found as file
                    $_dbMap->setRemoved();
                    array_push($mapsToPersist, $_dbMap);
                }
            }

            if (in_array($_dbMap->getName(), $this->originMapNames)) {
                if (!$_dbMap->isOrigin()) {
                    $_dbMap->setOrigin();

                    if (!in_array($_dbMap, $mapsToPersist)) {
                        array_push($mapsToPersist, $_dbMap);
                    }
                }
            } else {
                if ($_dbMap->isOrigin()) {
                    $_dbMap->setOrigin(false);

                    if (!in_array($_dbMap, $mapsToPersist)) {
                        array_push($mapsToPersist, $_dbMap);
                    }
                }
            }
        }

        // add all new maps
        foreach ($currentMapFileNames as $_mapFileName) {
            $_newMap = Map::create()->setName($_mapFileName);

            if (in_array($_mapFileName, $this->originMapNames)) {
                $_newMap->setOrigin();
            }

            array_push($mapsToPersist, $_newMap);
        }

        // calculate new/deleted maps
        $insertedMaps = array_filter($mapsToPersist, function (Map $map) {
            return !$map->isRemoved();
        });

        $removedMaps = array_filter($mapsToPersist, function (Map $map) {
            return $map->isRemoved();
        });

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {

            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {

                if (empty($insertedMaps)) {
                    $output->writeln('  > no maps to insert found');
                } else {
                    $output->writeln(sprintf('  > %d map(s) will be inserted:%s', count($insertedMaps), implode(array_map(function (Map $_map) {
                        return sprintf('%s    * %s', PHP_EOL, $_map->getName());
                    }, $insertedMaps))));
                }

                if (empty($removedMaps)) {
                    $output->writeln('  > no maps to delete found');
                } else {
                    $output->writeln(sprintf('  > %d map(s) will be deleted:%s', count($removedMaps), implode(array_map(function (Map $_map) {
                        return sprintf('%s    * %s', PHP_EOL, $_map->getName());
                    }, $removedMaps))));
                }
                $output->writeln('<info>done</info>');

            } elseif ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {

                if (empty($insertedMaps)) {
                    $output->writeln('  > no maps to insert found');
                } else {
                    $output->writeln(sprintf('  > %d map(s) will be inserted', count($insertedMaps)));
                }

                if (empty($removedMaps)) {
                    $output->writeln('  > no maps to delete found');
                } else {
                    $output->writeln(sprintf('  > %d map(s) will be deleted', count($removedMaps)));
                }
                $output->writeln('<info>done</info>');
            } else {
                $output->writeln(' <info>done</info>');
            }
        }

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $output->write('<comment>4. Committing all changes to database...</comment> ');
        }

        if ($dryRun) {

            $output->writeln(' <error>skip (option --dry-run)</error>');

            return $mapsToPersist;

        }

        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $output->writeln('<info>done</info>');
        }

        foreach ($mapsToPersist as $_map) {
            $this->entityManager->persist($_map);
        }

        $this->entityManager->flush();

        $this->logService->logSyncUpdate($insertedMaps, $removedMaps);

        return $mapsToPersist;
    }

}