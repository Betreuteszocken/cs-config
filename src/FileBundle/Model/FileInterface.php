<?php

namespace Betreuteszocken\CsConfig\FileBundle\Model;

/**
 * Class MapInterface
 *
 * @package Betreuteszocken\CsConfig\FileBundle
 */
interface FileInterface
{
    /**
     * Returns the absolute path of the map file.
     *
     * @return string
     */
    public function getPath();
}