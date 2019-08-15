<?php

namespace Betreuteszocken\CsConfig\DBAL;

use Doctrine\DBAL\Types\SmallIntType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Class LogType
 *
 * @package Betreuteszocken\CsConfig
 */
class LogType extends SmallIntType
{
    const TYPE_COMMON = 0;
    const TYPE_SYNC_MAPS = 10;
    const TYPE_MAP_CYCLE_NEW = 20;
    const TYPE_CYCLE_CONFIG_UPDATE = 30;
    const TYPE_BZ_CONFIG_UPDATE = 40;

    /**
     * {@inheritDoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getSmallIntTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            return null;
        }

        if (!$this->isValidValue($value)) {
            throw new \InvalidArgumentException("Invalid contact type");
        }
        return $value;
    }

    /**
     * @param int|null $value
     *
     * @return bool
     */
    protected function isValidValue(?int $value): bool
    {
        return
            array_sum(
                array_map(
                    function ($_contactType) use ($value) {
                        return $_contactType & $value;
                    },
                    $this->getValidValues()
                )
            ) > 0;
    }

    /**
     * @return int[]
     */
    protected function getValidValues(): array
    {
        return [
            self::TYPE_COMMON,
            self::TYPE_SYNC_MAPS,
            self::TYPE_MAP_CYCLE_NEW,
            self::TYPE_CYCLE_CONFIG_UPDATE,
            self::TYPE_BZ_CONFIG_UPDATE,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'log_type';
    }

    /**
     * {@inheritDoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}