<?php

namespace Betreuteszocken\CsConfig\FileBundle\Model;

/**
 * Class Map
 *
 * @package Betreuteszocken\CsConfig\FileBundle
 */
class File implements FileInterface
{

    /**
     * the (absolute) of the map file
     *
     * @var string
     */
    protected $path = '';

    /**
     * @param string $path the (absolute) of the map file
     */
    public function __construct(string $path = '')
    {
        $this->path = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function getPath()
    {
        return $this->path;
    }
}