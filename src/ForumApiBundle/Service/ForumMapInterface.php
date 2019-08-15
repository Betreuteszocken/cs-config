<?php

namespace Betreuteszocken\CsConfig\ForumApiBundle\Service;

/**
 * Interface ForumMapInterface
 *
 * @package Betreuteszocken\CsConfig\ForumApiBundle
 */
interface ForumMapInterface
{
    /**
     * Returns the amount of mentioned user preferred maps indexed by map name.
     *
     * @return int[]
     */
    public function getMapCounts(): array;
}