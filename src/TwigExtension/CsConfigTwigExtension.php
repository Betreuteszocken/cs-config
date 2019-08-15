<?php

namespace Betreuteszocken\CsConfig\TwigExtension;

use Betreuteszocken\CsConfig\DBAL\LogType;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class CsConfigTwigExtension
 *
 * @package Betreuteszocken\CsConfig
 */
class CsConfigTwigExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('logType', [$this, 'renderLogType'], ['is_safe' => ['html']]),
        );
    }

    /**
     * @param int $logType
     *
     * @return string
     */
    public function renderLogType(int $logType): string
    {
        switch ($logType) {
            case LogType::TYPE_MAP_CYCLE_NEW:
                return 'New map cycle';
            case LogType::TYPE_SYNC_MAPS:
                return 'Maps synced';
            case LogType::TYPE_CYCLE_CONFIG_UPDATE:
                return 'Map cycle config updated';
            case LogType::TYPE_BZ_CONFIG_UPDATE:
                return 'BZ config updated';
            default:
                return 'Common event';
        }
    }
}