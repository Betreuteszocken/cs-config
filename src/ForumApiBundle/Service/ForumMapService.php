<?php

namespace Betreuteszocken\CsConfig\ForumApiBundle\Service;

use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ForumMapService
 *
 * @package Betreuteszocken\CsConfig\ForumApiBundle
 */
class ForumMapService implements ForumMapInterface
{
    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var integer
     */
    protected $days = 0;

    /**
     * ForumMapService constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->url  = $container->getParameter('forum_api.url');
        $this->days = $container->getParameter('forum_api.days');
    }

    /**
     * {@inheritDoc}
     */
    public function getMapCounts(): array
    {
        $options = ['http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query(['days' => $this->days])
        ]];

        $context = stream_context_create($options);

        $encodedMaps = file_get_contents($this->url, false, $context);

        /** @var stdClass[] $jsonMaps */
        $jsonMaps = json_decode($encodedMaps);

        $mapCount = array();

        foreach ($jsonMaps as $_jsonMap) {
            $mapCount[$_jsonMap->name] = (int)$_jsonMap->count;
        }

        return array_filter($mapCount);
    }
}