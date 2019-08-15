<?php

namespace Betreuteszocken\CsConfig\FileBundle;

use Betreuteszocken\CsConfig\FileBundle\DependencyInjection\FileBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class FileBundle
 *
 * @package Betreuteszocken\CsConfig\FileBundle
 */
class FileBundle extends Bundle
{

    public function getContainerExtension()
    {
        return new FileBundleExtension();
    }
}
