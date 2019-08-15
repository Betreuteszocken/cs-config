<?php

namespace Betreuteszocken\CsConfig\Command;

use Betreuteszocken\CsConfig\Entity\CycleConfig;
use Betreuteszocken\CsConfig\Entity\Map;
use Betreuteszocken\CsConfig\Entity\MapCategory;
use Betreuteszocken\CsConfig\Service\CycleGenerator;
use Betreuteszocken\CsConfig\Service\LogService;
use Betreuteszocken\CsConfig\Service\MapCycleTxtGenerator;
use Betreuteszocken\CsConfig\Service\MapService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class MapcycleGenerateCommand
 *
 * @package Betreuteszocken\CsConfig
 */
class MapcycleGenerateCommand extends Command
{

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager = null;

    /**
     * @var CycleGenerator
     */
    protected $cycleGenerator = null;

    /**
     * @var MapService
     */
    protected $mapService = null;

    /**
     * @var MapCycleTxtGenerator
     */
    protected $mapCycleTxtGenerator = null;

    /**
     * @var LogService
     */
    protected $logService = null;

    /**
     * SriUpdateCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CycleGenerator         $cycleGenerator
     * @param LogService             $logService
     *
     * {@inheritdoc}
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CycleGenerator $cycleGenerator,
        MapService $mapService,
        MapCycleTxtGenerator $mapCycleTxtGenerator,
        LogService $logService,
        string $name = null)
    {
        $this->entityManager        = $entityManager;
        $this->cycleGenerator       = $cycleGenerator;
        $this->mapService           = $mapService;
        $this->mapCycleTxtGenerator = $mapCycleTxtGenerator;
        $this->logService           = $logService;

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mapcycle:generate')
            ->setDescription('Creates a new map cycle out of the current map cycle config.')
            ->setDefinition([
                new InputOption('dry-run', 'd', InputOption::VALUE_NONE, 'to run the command without saving the generated map cycle'),
            ])
            ->setHelp("
  <info>%command.full_name%</info> creates a new map cycle out of the current map cycle config.

Hint: Use one of the verbose modes to get more information while running the command. 
"
            )
        ;

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dryRun = $input->getOption('dry-run');

        $io = new SymfonyStyle($input, $output);

        $io->writeln(sprintf('[%s]', date_create()->format('Y-m-m H:i:s')));

        // 1. get current config

        $currentCycleConfig = $this->entityManager->getRepository(CycleConfig::class)->findCurrent();

        if ($io->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $io->writeln(sprintf('<comment>Step 1: Read current map cycle config </comment><info>(ID %d, last update %s)</info>', $currentCycleConfig->getId(), $currentCycleConfig->getCreatedAt()->format('Y-m-d H:i:s')));
        }

        $this->outputConfig($currentCycleConfig, $io);

        if ($io->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $io->writeln('<info>done</info>');
        }

        // 2. create mapcycle

        if ($io->getVerbosity() == OutputInterface::VERBOSITY_NORMAL) {
            $io->write('<comment>Step 2: Create next map cycle</comment>');
        } elseif ($io->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
            $io->writeln('<comment>Step 2: Create next map cycle</comment>');
        }

        $cycle = $this->cycleGenerator->create($currentCycleConfig ?? CycleConfig::create(), $output);

        if ($io->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
            $io->writeln('<info>done</info>');
        }

        if (!empty($cycle->getTotalMapCount())) {

            // 3. copy map cycle

            if ($io->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                $io->write('<comment>Step 3: Copy and save the map cycle config to use for the next cycle</comment>');
            }

            $newCycleConfig = clone $currentCycleConfig;

            if (!$dryRun) {
                $this->entityManager->persist($newCycleConfig);
                $this->entityManager->flush();

                if ($io->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                    $io->writeln(' <info>done</info>');
                }

            } elseif ($io->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                $io->writeln(' <error>skip (option --dry-run)</error>');
            }

            // 4. create txt file

            $io->write('<comment>Step 4: Create mapcycle.txt content and save map cycle to database</comment>');

            if (!$dryRun) {
                $this->entityManager->persist($cycle);
                $this->entityManager->flush();
                $this->entityManager->refresh($cycle);

                if ($io->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                    $io->writeln(sprintf(' <info>(new ID %d)</info>', $cycle->getId()));
                    $io->write($cycle->getMapcycleTxt());

                }
            } elseif ($io->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL) {
                $io->writeln(' <error>no save (option --dry-run)</error>');
                $io->writeln($this->mapCycleTxtGenerator->create($cycle));
            }
        }

        // 5. save log
        if (!$dryRun) {
            $this->logService->logNewMapcycle($cycle->getMapcycleTxt());
        }

        // result

        if ($dryRun) {
            $io->success('New mapcycle successfully created - but not saved in database. Usage of option --dry-run.');
        } else {
            $io->success(sprintf('New mapcycle (ID %d) successfully created.', $cycle->getId()));
        }

        return 0;
    }

    protected function outputConfig(CycleConfig $cycleConfig, SymfonyStyle $io)
    {
        if ($io->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {

            if ($io->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $io->writeln(sprintf("  > %d Mami Map(s):", $cycleConfig->getMamiMaps()->count()));
                array_map(function (Map $_map) use ($io) {
                    $io->writeln(sprintf('   - %s', $_map->getName()));
                }, $cycleConfig->getMamiMaps()->toArray());
            } else {
                $io->writeln(sprintf("  > %d Mami Map(s): %s ", $cycleConfig->getMamiMaps()->count(), implode(', ', array_map(function (Map $_map) {
                    return $_map->getName();
                }, $cycleConfig->getMamiMaps()->toArray()))));
            }


            if ($io->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $forumMaps = $this->mapService->getCountedMaps(true);

                $total = array_reduce($forumMaps, function (int $_carry, Map $_map) {
                    return $_carry + $_map->getCount();
                }, 0);

                $io->writeln(sprintf("  > %d aus %d Forum-Maps (%d Nennungen):", $cycleConfig->getUserMaps(), count($forumMaps), $total));
                array_map(function (Map $_map) use ($io) {
                    $io->writeln(sprintf('   - %s (%d %s)', $_map->getName(), $_map->getCount(), $_map->getCount() > 1 ? "Nennungen" : "Nennung"));
                }, $forumMaps);

            } else {
                $io->writeln(sprintf("  > %d Anzahl Forum-Maps", $cycleConfig->getUserMaps()));
            }

            if ($io->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {
                $defaultMaps = $this->entityManager->getRepository(Map::class)->findAllDefault();

                $io->writeln(sprintf("  > %d aus %d Standard BZ Maps:", $cycleConfig->getDefaultMaps(), count($defaultMaps)));
                array_map(function (Map $_map) use ($io) {
                    $io->writeln(sprintf('   - %s', $_map->getName()));
                }, $defaultMaps);

            } else {
                $io->writeln(sprintf("  > %d Standard BZ Maps", $cycleConfig->getDefaultMaps()));
            }

            if ($io->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {

                $defaultMapCategories = $this->entityManager->getRepository(MapCategory::class)->findAllDefault();

                $total = array_reduce($defaultMapCategories, function (int $_total, MapCategory $_mapCategory) {
                    return $_total + count($_mapCategory->getMaps());
                }, 0);

                $io->writeln(sprintf("  > %d aus %d Maps (aus %d Standard BZ Kategorien):", $cycleConfig->getDefaultCategoryMaps(), $total, count($defaultMapCategories)));
                array_map(function (MapCategory $_mapCategory) use ($io) {
                    $io->writeln(sprintf('   - %s (%d Maps)', $_mapCategory->getName(), count($_mapCategory->getMaps())));
                }, $defaultMapCategories);

            } else {
                $io->writeln(sprintf("  > %d Maps aus Standard BZ Kategorien", $cycleConfig->getDefaultCategoryMaps()));
            }

            if ($io->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {

                $originMaps = $this->entityManager->getRepository(Map::class)->findAllOrigin();

                $io->writeln(sprintf("  > %d aus %d originalen CS1.6 Maps:", $cycleConfig->getOriginMaps(), count($originMaps)));
                array_map(function (Map $_map) use ($io) {
                    $io->writeln(sprintf('   - %s', $_map->getName()));
                }, $originMaps);

            } else {
                $io->writeln(sprintf("  > %d originale CS1.6 Maps", $cycleConfig->getOriginMaps()));
            }

            if ($io->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE) {

                $allMaps = $this->entityManager->getRepository(Map::class)->findAllByRemovedFlag(false);

                $io->writeln(sprintf("  > %d Zufalls-Maps Maps aus allen %d vorhandenen Maps", $cycleConfig->getRandomMaps(), count($allMaps)));
            } else {
                $io->writeln(sprintf("  > %d Zufalls-Maps Maps aus allen vorhandenen Maps", $cycleConfig->getRandomMaps()));
            }

            $io->writeln(sprintf("  TOTAL: %d Maps", $cycleConfig->getTotalMaps()));
        }

    }
}