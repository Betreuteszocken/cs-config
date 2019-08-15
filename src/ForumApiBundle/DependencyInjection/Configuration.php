<?php

namespace Betreuteszocken\CsConfig\ForumApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Betreuteszocken\CsConfig\ForumApiBundle
 */
class Configuration implements ConfigurationInterface
{
    /**
     * amount of days of the last activity a user is treated as valid
     */
    const DEFAULT_ACTIVE_USER = 14;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('forum_api');

        // @formatter:off
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('url')
                    ->info('The url to request the json api end point.')
                    ->isRequired()
                    ->example('https://www.example.com/index.php?maps.json')
                ->end()
                ->integerNode('days')
                    ->info('The last activity of an users to count his favorite map is maximum *days* in the past.')
                    ->defaultValue(self::DEFAULT_ACTIVE_USER)
                    ->treatNullLike(self::DEFAULT_ACTIVE_USER)
                ->end()
            ->end();
        // @formatter:on

        return $treeBuilder;
    }
}