<?php

namespace Betreuteszocken\CsConfig\Controller;

use Betreuteszocken\CsConfig\Entity\Map;
use Betreuteszocken\CsConfig\Entity\MapCategory;
use Betreuteszocken\CsConfig\Service\MapFileService;
use Betreuteszocken\CsConfig\Service\MapService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MapController
 *
 * @package Betreuteszocken\CsConfig
 */
class MapController extends AbstractController
{
    /**
     * @var MapFileService
     */
    protected $mapFileService = null;

    /**
     * @var MapService
     */
    protected $mapService = null;

    /**
     * MapController constructor.
     *
     * @param MapFileService $mapFileService
     * @param MapService     $mapService
     */
    public function __construct(MapFileService $mapFileService, MapService $mapService)
    {
        $this->mapFileService = $mapFileService;
        $this->mapService     = $mapService;
    }

    /**
     * @return Response
     */
    public function indexMapsAction(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $mapCategories = $entityManager->getRepository(MapCategory::class)->findAll();

        // replace this example code with whatever you need
        return $this->render('map/category.html.twig', [
            'mapCategories' => $mapCategories,
        ]);
    }

    /**
     * @return Response
     */
    public function indexMapsLexigraphicallyAction(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $maps = $entityManager->getRepository(Map::class)->findAllByRemovedFlag(false);

        // replace this example code with whatever you need
        return $this->render('map/lexigraphically.html.twig', [
            'maps' => $maps,
        ]);
    }

    /**
     * @return Response
     */
    public function indexUserMapsAction(): Response
    {
        $maps = $this->mapService->getCountedMaps(true);

        $total = array_reduce($maps, function (int $_carry, Map $_map) {
            return $_carry + $_map->getCount();
        }, 0);

        // replace this example code with whatever you need
        return $this->render('map/user.html.twig', [
            'maps'  => $maps,
            'total' => $total,
        ]);
    }
}