<?php

namespace Betreuteszocken\CsConfig\Service;

use Betreuteszocken\CsConfig\FileBundle\Service\FileService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MapFileService
 *
 * @package Betreuteszocken\CsConfig
 */
class MapFileService
{
    /**
     * @var FileService
     */
    protected $fileService = null;

    /**
     * @var string
     */
    protected $mapPath = '';

    /**
     * MapService constructor.
     *
     * @param ContainerInterface $container
     * @param FileService        $fileService
     */
    public function __construct(ContainerInterface $container, FileService $fileService)
    {
        $this->fileService = $fileService;
        $this->mapPath     = $container->getParameter('bz.cs_config.map_dir');
    }

    /**
     * @return string[]
     */
    public function getMapFileNames(): array
    {
        $files = $this->fileService->getFiles($this->mapPath, 'bsp');

        $mapFileNames = array();

        foreach ($files as $_file) {

            $_pathInfo = pathinfo($_file->getPath(), PATHINFO_BASENAME);

            $_mapName = preg_filter('/^(.+)\.bsp$/i', '$1', $_pathInfo);

            $mapFileNames[$_mapName] = $_mapName;
        }

        return $mapFileNames;
    }
}
