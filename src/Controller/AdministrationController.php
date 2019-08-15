<?php

namespace Betreuteszocken\CsConfig\Controller;

use Betreuteszocken\CsConfig\Entity\CycleConfig;
use Betreuteszocken\CsConfig\Entity\Log;
use Betreuteszocken\CsConfig\Entity\Map;
use Betreuteszocken\CsConfig\Entity\MapCategory;
use Betreuteszocken\CsConfig\Form\Type\DefaultConfig\DefaultConfigFormType;
use Betreuteszocken\CsConfig\Model\Form\DefaultConfig\DefaultConfigFormModel;
use Betreuteszocken\CsConfig\Service\CycleGenerator;
use Betreuteszocken\CsConfig\Service\LogService;
use Betreuteszocken\CsConfig\Service\MapFileService;
use Betreuteszocken\CsConfig\Service\MapsSynchronizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdministrationController
 *
 * @package Betreuteszocken\CsConfig
 */
class AdministrationController extends AbstractController
{
    /**
     * @var MapFileService
     */
    protected $mapFileService = null;

    /**
     * @var LogService
     */
    protected $logService = null;

    /**
     * @var CycleGenerator
     */
    protected $cycleGenerator = null;

    /**
     * @var string[]
     */
    protected $originMapNames = array();

    public function __construct(LogService $logService, MapFileService $mapService, CycleGenerator $cycleGenerator, ContainerInterface $container)
    {
        $this->mapFileService = $mapService;
        $this->logService     = $logService;
        $this->cycleGenerator = $cycleGenerator;
        $this->originMapNames = array_filter(array_map('trim', $container->getParameter('bz.cs_config.origin_maps')));
    }

    public function indexAction()
    {
        // replace this example code with whatever you need
        return $this->render('administration/index.html.twig');
    }

    public function logsAction()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $lastLogs = $entityManager->getRepository(Log::class)->findLastLogs();

        // replace this example code with whatever you need
        return $this->render('administration/logs.html.twig', [
            'logs' => $lastLogs
        ]);
    }

    public function recreateCycleAction()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $currentCycleConfig = $entityManager->getRepository(CycleConfig::class)->findCurrent();

        $cycle = $this->cycleGenerator->create($currentCycleConfig ?? CycleConfig::create());

        if (!empty($cycle->getTotalMapCount())) {

            $newCycleConfig = clone $currentCycleConfig;

            $entityManager->persist($newCycleConfig);
            $entityManager->persist($cycle);
            $entityManager->flush();
        }
//        else
//        {
//            // flash message: no maps available
//        }

        $this->logService->logNewMapcycle($cycle->getMapcycleTxt());

        // replace this example code with whatever you need
        return $this->redirectToRoute('map-cycles');
    }

    /**
     * @param MapsSynchronizer $mapsSynchronizer
     *
     * @return RedirectResponse
     */
    public function syncMapFilesAction(MapsSynchronizer $mapsSynchronizer)
    {
        $maps = $mapsSynchronizer->sync();

//        if(empty($maps))
//        {
//            flash message!
//        }
//        else
//        {
//            flash message!
//        }

        return $this->redirectToRoute('maps');
    }


    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateDefaultConfigAction(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $previousDefaultMaps          = $entityManager->getRepository(Map::class)->findAllDefault(false);
        $previousDefaultMapCategories = $entityManager->getRepository(MapCategory::class)->findAllDefault();

        $modelDefaultConfig = new DefaultConfigFormModel();
        $modelDefaultConfig->setMaps($previousDefaultMaps);
        $modelDefaultConfig->setMapCategories($previousDefaultMapCategories);

        $form = $this->createForm(DefaultConfigFormType::class, $modelDefaultConfig)->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->render('administration/default-config.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        if (!$form->isValid()) {
//            $this->addFlash(
//                'danger',
//                $this->renderView('alert.html.twig', [
//                    'message' => 'Fehler beim Speichern des Betriebs',
//                    'form_errors' => $form->getErrors(true),
//                ])
//            );
            return $this->render('administration/default-config.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        $newDefaultMaps             = array();
        $removeDefaultMaps          = array();
        $newDefaultMapCategories    = array();
        $removeDefaultMapCategories = array();

        // 1. update default BZ maps

        foreach (array_diff($modelDefaultConfig->getMaps(), $previousDefaultMaps) as $_newDefaultMap) {
            /** @var Map $_newDefaultMap */
            $_newDefaultMap->setDefault();
            array_push($newDefaultMaps, $_newDefaultMap);
            $entityManager->persist($_newDefaultMap);
        }

        foreach (array_diff($previousDefaultMaps, $modelDefaultConfig->getMaps()) as $_removedDefaultMap) {
            /** @var Map $_removedDefaultMap */
            $_removedDefaultMap->setDefault(false);
            array_push($removeDefaultMaps, $_removedDefaultMap);
            $entityManager->persist($_removedDefaultMap);
        }


        // 2. update default BZ map categories

        foreach (array_diff($modelDefaultConfig->getMapCategories(), $previousDefaultMapCategories) as $_newDefaultMapCategory) {
            /** @var MapCategory $_newDefaultMapCategory */
            $_newDefaultMapCategory->setDefault();
            array_push($newDefaultMapCategories, $_newDefaultMapCategory);
            $entityManager->persist($_newDefaultMapCategory);
        }

        foreach (array_diff($previousDefaultMapCategories, $modelDefaultConfig->getMapCategories()) as $_removedDefaultMapCategory) {
            /** @var MapCategory $_removedDefaultMapCategory */
            $_removedDefaultMapCategory->setDefault(false);
            array_push($removeDefaultMapCategories, $_removedDefaultMapCategory);
            $entityManager->persist($_removedDefaultMapCategory);
        }

        $this->logService->logUpdateBzDefaultSettings(
            $newDefaultMaps,
            $removeDefaultMaps,
            $newDefaultMapCategories,
            $removeDefaultMapCategories
        );

        $entityManager->flush();

        return $this->redirectToRoute('admin-default-config-update');
    }
}