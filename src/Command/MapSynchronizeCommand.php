<?php

namespace Betreuteszocken\CsConfig\Command;

use Betreuteszocken\CsConfig\Service\LogService;
use Betreuteszocken\CsConfig\Service\MapFileService;
use Betreuteszocken\CsConfig\Service\MapsSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class MapSynchronizeCommand
 *
 * @package Betreuteszocken\CsConfig
 */
class MapSynchronizeCommand extends Command
{

    /**
     * @var MapsSynchronizer
     */
    protected $mapSynchronizer = null;

    /**
     * SriUpdateCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param MapFileService         $mapFileService
     * @param LogService             $logService
     *
     * {@inheritdoc}
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(MapsSynchronizer $mapSynchronizer, string $name = null)
    {
        $this->mapSynchronizer = $mapSynchronizer;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('maps:synchronize')
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

        $persistedMaps = $this->mapSynchronizer->sync($dryRun, $output);

        if (empty($persistedMaps)) {
            $io->success('Maps successfully synchronized - no differences detected.');
        } elseif ($dryRun) {
            $io->success('Maps successfully synchronized - but database was not updated. Usage of option --dry-run.');
        } else {
            $io->success('Maps successfully synchronized.');
        }

        return 0;
    }
}