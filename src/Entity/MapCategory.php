<?php

namespace Betreuteszocken\CsConfig\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Map
 *
 * @ORM\Entity(repositoryClass="\Betreuteszocken\CsConfig\Repository\MapCategoryRepository")
 * @ORM\Table(name="map_category")
 *
 * @package Betreuteszocken\CsConfig
 */
class MapCategory extends BaseEntity
{
    /**
     * the basename of the map, for instance `de_dust2`
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=63, unique=true)
     */
    protected $name = '';

    /**
     * @var string
     *
     * @ORM\Column(name="regex", type="string", length=63, unique=true)
     */
    protected $regex = '';

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="`default`", type="date", nullable=true)
     */
    protected $default = null;

    /**
     * @var Map[]
     */
    protected $maps = array();

    /**
     * Implemented to be used in `array_diff()` and other `array_*` functions.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return MapCategory
     */
    public function setName($name): MapCategory
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * @param string $regex
     *
     * @return MapCategory
     */
    public function setRegex(string $regex): MapCategory
    {
        $this->regex = $regex;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDefault(): bool
    {
        return !is_null($this->default);
    }

    /**
     * @return DateTime|null
     */
    public function getDefaultValue(): ?DateTime
    {
        return $this->default;
    }

    /**
     * @param boolean $default
     *
     * @return MapCategory
     */
    public function setDefault(bool $default = true): MapCategory
    {
        $this->default = $default ? date_create() : null;
        return $this;
    }

    /**
     * @return Map[]
     */
    public function getMaps(): array
    {
        return $this->maps;
    }

    /**
     * @param Map[] $maps
     *
     * @return MapCategory
     */
    public function setMaps(array $maps): MapCategory
    {
        $this->maps = $maps;
        return $this;
    }

    /**
     * @param Map $map
     *
     * @return MapCategory
     */
    public function addMap(Map $map): MapCategory
    {
        array_push($this->maps, $map);
        return $this;
    }
}