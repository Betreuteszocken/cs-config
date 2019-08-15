<?php

namespace Betreuteszocken\CsConfig\EventListener;

use Betreuteszocken\CsConfig\Entity\Cycle;
use Betreuteszocken\CsConfig\Service\MapCycleTxtGenerator;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class CycleSubscriber is responsible to generate the `mapcycle.txt` file content
 * if an object of class {@see \Betreuteszocken\CsConfig\Entity\Cycle} is saved.
 *
 * @package Betreuteszocken\CsConfig
 */
class CycleSubscriber implements EventSubscriber
{
    /**
     * @var MapCycleTxtGenerator
     */
    protected $mapCycleTxtGenerator = null;

    /**
     * MapCycleListener constructor.
     *
     * @param MapCycleTxtGenerator $mapCycleTxtGenerator
     */
    public function __construct(MapCycleTxtGenerator $mapCycleTxtGenerator)
    {
        $this->mapCycleTxtGenerator = $mapCycleTxtGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $cycle = $args->getObject();

        if (!$cycle instanceof Cycle) {
            return;
        }

        $mapCycleTxt = $this->mapCycleTxtGenerator->create($cycle);

        $cycle->setMapcycleTxt($mapCycleTxt);
    }
}