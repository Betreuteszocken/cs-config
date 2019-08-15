<?php

namespace Betreuteszocken\CsConfig\Service;

use Betreuteszocken\CsConfig\Entity\Cycle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class MapCycleTxtGenerator
 *
 * @package Betreuteszocken\CsConfig
 */
class MapCycleTxtGenerator
{
    /**
     * @var Environment
     */
    protected $templateEnvironment = null;

    /**
     * @var int
     */
    protected $days = null;

    /**
     * MapCycleTxtGenerator constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->templateEnvironment = $container->get('twig');
        $this->days                = $container->getParameter('forum_api.days');
    }

    /**
     * @param Cycle $cycle
     * @return string
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Cycle $cycle): string
    {
        return $this->templateEnvironment->render('administration/mapcycle.txt.twig', [
            'mamiMaps'            => $cycle->getMamiMaps(),
            'userMaps'            => $cycle->getUserMaps(),
            'userDays'            => $this->days,
            'defaultMaps'         => $cycle->getDefaultMaps(),
            'defaultCategoryMaps' => $cycle->getDefaultCategoryMaps(),
            'originMaps'          => $cycle->getOriginMaps(),
            'randomMaps'          => $cycle->getRandomMaps(),
        ]);
    }
}