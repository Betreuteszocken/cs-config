<?php

namespace Betreuteszocken\CsConfig\Model\Form\DefaultConfig;

use Betreuteszocken\CsConfig\Entity\Map;
use Betreuteszocken\CsConfig\Entity\MapCategory;

/**
 * Class DefaultConfigFormModel
 *
 * @package Betreuteszocken\CsConfig
 */
class DefaultConfigFormModel
{
    /**
     * @var Map[]
     */
    protected $maps = array();

    /**
     * @var MapCategory[]
     */
    protected $mapCategories = array();

    /**
     * @return Map[]
     */
    public function getMaps(): array
    {
        return $this->maps;
    }

    /**
     * @param Map[] $maps
     *
     * @return DefaultConfigFormModel
     */
    public function setMaps(array $maps): DefaultConfigFormModel
    {
        $this->maps = $maps;
        return $this;
    }

    /**
     * @return MapCategory[]
     */
    public function getMapCategories(): array
    {
        return $this->mapCategories;
    }

    /**
     * @param MapCategory[] $mapCategories
     *
     * @return DefaultConfigFormModel
     */
    public function setMapCategories(array $mapCategories): DefaultConfigFormModel
    {
        $this->mapCategories = $mapCategories;
        return $this;
    }
}