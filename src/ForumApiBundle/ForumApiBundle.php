<?php

namespace Betreuteszocken\CsConfig\ForumApiBundle;

use Betreuteszocken\CsConfig\ForumApiBundle\DependencyInjection\ForumApiBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ForumApiBundle
 *
 * @package Betreuteszocken\CsConfig\ForumApiBundle
 */
class ForumApiBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $this->name = 'forum_api';
        parent::boot();
    }

    /**
     * {@inheritDoc}
     */
    public function getContainerExtension()
    {
        return new ForumApiBundleExtension();
    }
}
