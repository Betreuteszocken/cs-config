<?php

namespace Betreuteszocken\CsConfig\Exception;

class MultipleMapsException extends \RuntimeException
{
    /**
     * @var array
     */
    protected $multipleMaps = array();

    /**
     * MultipleMapsException constructor.
     *
     * {@inheritdoc}
     *
     * @param string[][] $multipleMaps
     */
    public function __construct($message = null, $statusCode = 0, \Throwable $previous = null, array $multipleMaps = array())
    {
        $this->multipleMaps = $multipleMaps;

        parent::__construct($message, $statusCode, $previous);
    }

    /**
     * @return string[][]
     */
    public function getMultipleMaps(): array
    {
        return $this->multipleMaps;
    }
}
