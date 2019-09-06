<?php

namespace Betreuteszocken\CsConfig\Service;

use Betreuteszocken\CsConfig\DBAL\LogType;
use Betreuteszocken\CsConfig\Entity\CycleConfig;
use Betreuteszocken\CsConfig\Entity\Log;
use Betreuteszocken\CsConfig\Entity\Map;
use Betreuteszocken\CsConfig\Entity\MapCategory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class LogService
 *
 * @package Betreuteszocken\CsConfig
 */
class LogService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager = null;

    /**
     * @var Security
     */
    protected $security = null;

    /**
     * @var LoggerInterface
     */
    protected $logger = null;

    /**
     * MapService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param Security               $security
     */
    public function __construct(EntityManagerInterface $entityManager, Security $security, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->security      = $security;
        $this->logger        = $logger;
    }


    /**
     * @param string $message
     * @param int    $type
     * @param bool   $error
     */
    protected function log(string $message, $type = LogType::TYPE_COMMON, bool $error = false): void
    {
        $log = Log::create()
                  ->setType($type)
                  ->setMessage($message)
                  ->setError($error)
        ;

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    /**
     * @param array $multipleMaps
     */
    public function logSyncUpdateErrorMultipleMaps(array $multipleMaps): void
    {
        $message = 'Error - Please remove duplicate maps (except of one) with the same name and run again:';
        foreach ($multipleMaps as $mapFileNames)
        {
            $message .= sprintf("\n * %s", implode(', ', $mapFileNames));
        }

        $this->log($message, LogType::TYPE_SYNC_MAPS, true);
    }

    /**
     * @param Map[] $insertedMaps
     * @param Map[] $deletedMaps
     */
    public function logSyncUpdate(array $insertedMaps = array(), array $deletedMaps = array()): void
    {
        $message = '';

        if (empty($insertedMaps) && empty($deletedMaps)) {
            $message = 'No maps to delete or to insert found.';
        } else {
            if (!empty($insertedMaps)) {
                $message .= sprintf('Successfully inserted following maps:%s%s%1$s', PHP_EOL, implode("\n", array_map(function (Map $_map) {
                    return sprintf('* %s', $_map->getName());
                }, $insertedMaps)));
            }
            if (!empty($deletedMaps)) {
                $message .= sprintf('Following maps have been deleted:%s%s%1$s', PHP_EOL, implode("\n", array_map(function (Map $_map) {
                    return sprintf('* %s', $_map->getName());
                }, $deletedMaps)));
            }
        }

        $this->log($message, LogType::TYPE_SYNC_MAPS);
    }

    protected function getCycleConfigPropertyMessage(string $mapName, int $newValue, ?int $oldValue): string
    {
        if (is_null($oldValue)) {
            return sprintf('Amount of %s: %d (no change)', $mapName, $newValue);
        } else {
            return sprintf('Changed amount of %s from "%d" to "%d"', $mapName, $newValue, $oldValue);
        }
    }

    /**
     * @param CycleConfig $cycleConfig
     * @param Map[]       $newMamiMaps
     * @param Map[]       $removeMamiMaps
     */
    public function logUpdateMapCycleConfig(CycleConfig $cycleConfig, array $newMamiMaps, array $removeMamiMaps): void
    {
        $unitOfWork = $this->entityManager->getUnitOfWork();

        // special case: first cycle to save in database / no previous one exists
        if (is_null($cycleConfig->getId())) {
            $message = 'New map cycle configuration';
        } else {

            $unitOfWork->computeChangeSet($this->entityManager->getClassMetadata(CycleConfig::class), $cycleConfig);

            if (!$unitOfWork->isScheduledForUpdate($cycleConfig)) {
                return;
            }

            $message = sprintf('Changed map cycle configuration (cycle ID #%d):', $cycleConfig->getId());
        }


        $message .= sprintf('%s* Mami map(s): %s', PHP_EOL, implode(', ', array_map(function (Map $_map) {
            return $_map->getName();
        }, $cycleConfig->getMamiMaps()->toArray())));

        if (!empty($removeMamiMaps) || !empty($newMamiMaps)) {
            $message .= sprintf('%s  (%s%s%s%s%s)',
                PHP_EOL,
                !empty($removeMamiMaps) ? 'removed: ' : '',
                implode(',', array_map(function (Map $_map) {
                    return $_map->getName();
                }, $removeMamiMaps)),
                (!empty($removeMamiMaps) && !empty($newMamiMaps)) ? ' / ' : '',
                !empty($newMamiMaps) ? 'added: ' : '',
                implode(', ', array_map(function (Map $_map) {
                    return $_map->getName();
                }, $newMamiMaps))
            );
        } else {
            $message .= sprintf('%s  (no change)', PHP_EOL);
        }

        $changeSet = $unitOfWork->getEntityChangeSet($cycleConfig);

        $message .= sprintf('%s* %s', PHP_EOL, $this->getCycleConfigPropertyMessage(
            'default maps',
            $cycleConfig->getDefaultMaps(),
            array_key_exists('defaultMaps', $changeSet) ? $changeSet['defaultMaps'][0] : null
        ));

        $message .= sprintf('%s* %s', PHP_EOL, $this->getCycleConfigPropertyMessage(
            'default map categories',
            $cycleConfig->getDefaultCategoryMaps(),
            array_key_exists('defaultCategoryMaps', $changeSet) ? $changeSet['defaultCategoryMaps'][0] : null
        ));

        $message .= sprintf('%s* %s', PHP_EOL, $this->getCycleConfigPropertyMessage(
            'origin CS1.6 maps',
            $cycleConfig->getOriginMaps(),
            array_key_exists('originMaps', $changeSet) ? $changeSet['originMaps'][0] : null
        ));

        $message .= sprintf('%s* %s', PHP_EOL, $this->getCycleConfigPropertyMessage(
            'random maps',
            $cycleConfig->getRandomMaps(),
            array_key_exists('randomMaps', $changeSet) ? $changeSet['randomMaps'][0] : null
        ));

        $this->log($message, LogType::TYPE_CYCLE_CONFIG_UPDATE);
    }

    /**
     * @param Map[]         $newDefaultMaps
     * @param Map[]         $removeDefaultMaps
     * @param MapCategory[] $newDefaultMapCategories
     * @param MapCategory[] $removeDefaultMapCategories
     */
    public function logUpdateBzDefaultSettings(
        $newDefaultMaps = array(),
        $removeDefaultMaps = array(),
        $newDefaultMapCategories = array(),
        $removeDefaultMapCategories = array()
    ): void
    {
        if (empty($newDefaultMaps) && empty($removeDefaultMaps) && empty($newDefaultMapCategories) && empty($removeDefaultMapCategories)) {
            return;
        }

        $message = 'Updated BZ default maps / default map categories:';

        if (!empty($newDefaultMaps)) {
            $message .= sprintf('%s* Marked following map(s) as default: %s', PHP_EOL, implode(', ', array_map(function (Map $_map) {
                return $_map->getName();
            }, $newDefaultMaps)));
        }

        if (!empty($removeDefaultMaps)) {
            $message .= sprintf('%s* Removed default tag from following map(s): %s', PHP_EOL, implode(', ', array_map(function (Map $_map) {
                return $_map->getName();
            }, $removeDefaultMaps)));
        }

        if (!empty($newDefaultMapCategories)) {
            $message .= sprintf('%s* Marked following map category/ies as default: %s', PHP_EOL, implode(', ', array_map(function (MapCategory $_mapCategory) {
                return $_mapCategory->getName();
            }, $newDefaultMapCategories)));
        }

        if (!empty($removeDefaultMapCategories)) {
            $message .= sprintf('%s* Removed default tag from following map category/ies: %s', PHP_EOL, implode(', ', array_map(function (MapCategory $_mapCategory) {
                return $_mapCategory->getName();
            }, $removeDefaultMapCategories)));
        }

        $this->log($message, LogType::TYPE_BZ_CONFIG_UPDATE);
    }

    /**
     * @param string $content the content of the `mapcycle.txt` file
     */
    public function logNewMapcycle(string $content): void
    {
        if (empty($content)) {
            return;
        }

        $this->log($content, LogType::TYPE_MAP_CYCLE_NEW);
    }
}
