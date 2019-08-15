<?php

namespace Betreuteszocken\CsConfig\Controller;

use Betreuteszocken\CsConfig\Entity\Cycle;
use Betreuteszocken\CsConfig\Entity\CycleConfig;
use Betreuteszocken\CsConfig\Form\Type\CycleConfig\CycleConfigFormType;
use Betreuteszocken\CsConfig\Service\LogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MapCycleController
 *
 * @package Betreuteszocken\CsConfig
 */
class MapCycleController extends AbstractController
{
    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $cycle = $entityManager->getRepository(Cycle::class)->findCurrent();

        // replace this example code with whatever you need
        return $this->render('map-cycle/index.html.twig', [
            'cycle' => $cycle,
        ]);

    }

    /**
     * @return Response
     */
    public function historyAction(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $cycles = $entityManager->getRepository(Cycle::class)->findAll();

        // replace this example code with whatever you need
        return $this->render('map-cycle/history.html.twig', [
            'cycles' => $cycles,
        ]);

    }

    /**
     * @param LogService $logService
     * @param Request    $request
     *
     * @return Response
     */
    public function updateCycleConfigAction(LogService $logService, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $cycleConfig = $entityManager->getRepository(CycleConfig::class)->findCurrent();

        if (is_null($cycleConfig)) {
            $cycleConfig = CycleConfig::create();
        }

        $originMamiMaps = $cycleConfig->getMamiMaps()->toArray();

        $form = $this
            ->createForm(CycleConfigFormType::class, $cycleConfig)
            ->handleRequest($request)
        ;

        if (!$form->isSubmitted()) {
            return $this->render('map-cycle/cycle-config.html.twig', [
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
            return $this->render('map-cycle/cycle-config.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        $logService->logUpdateMapCycleConfig(
            $cycleConfig,
            array_diff($cycleConfig->getMamiMaps()->toArray(), $originMamiMaps),
            array_diff($originMamiMaps, $cycleConfig->getMamiMaps()->toArray())
        );

        $entityManager->persist($cycleConfig);
        $entityManager->flush();

        // replace this example code with whatever you need
        return $this->redirectToRoute('map-cycle-update-config');
    }

    /**
     * @return Response
     */
    public function   mapcycleTxtAction(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $cycle = $entityManager->getRepository(Cycle::class)->findCurrent();

        return new Response($cycle->getMapcycleTxt(), 200, [
            'Content-Type'  => 'text/plain',
            // https://developer.mozilla.org/de/docs/Web/HTTP/Headers/Cache-Control#Caching_verhindern
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}