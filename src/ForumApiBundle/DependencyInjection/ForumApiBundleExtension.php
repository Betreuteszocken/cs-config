<?php

namespace Betreuteszocken\CsConfig\ForumApiBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

/**
 *
 * @link    http://symfony.com/doc/current/bundles/extension.html
 *
 * @package Betreuteszocken\CsConfig\ForumApiBundle
 */
class ForumApiBundleExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'forum_api';
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        /** @var LoaderInterface $loader */
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('forum_api.url', $config['url']);
        $container->setParameter('forum_api.days', $config['days']);
    }

}