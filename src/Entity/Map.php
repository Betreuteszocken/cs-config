<?php

namespace Betreuteszocken\CsConfig\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Map
 *
 * @ORM\Entity(repositoryClass="\Betreuteszocken\CsConfig\Repository\MapRepository")
 * @ORM\Table(name="map")
 *
 * @package Betreuteszocken\CsConfig
 */
class Map extends BaseEntity
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
     * @var DateTime|null
     *
     * @ORM\Column(name="origin", type="date", nullable=true)
     */
    protected $origin = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="`default`", type="date", nullable=true)
     */
    protected $default = null;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="removed", type="date", nullable=true)
     */
    protected $removed = null;

    /**
     * @var integer|null
     */
    protected $count = null;

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
     * @return Map
     */
    public function setName($name): Map
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isOrigin(): bool
    {
        return !is_null($this->origin);
    }

    /**
     * @return DateTime|null
     */
    public function getOriginValue(): ?DateTime
    {
        return $this->origin;
    }

    /**
     * @param bool $origin
     *
     * @return Map
     */
    public function setOrigin(bool $origin = true): Map
    {
        $this->origin = $origin ? date_create() : null;
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
     * @return Map
     */
    public function setDefault(bool $default = true): Map
    {
        $this->default = $default ? date_create() : null;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRemoved(): bool
    {
        return !is_null($this->removed);
    }

    /**
     * @return DateTime|null
     */
    public function getRemovedValue(): ?DateTime
    {
        return $this->removed;
    }

    /**
     * @param bool $removed
     *
     * @return Map
     */
    public function setRemoved(bool $removed = true): Map
    {
        $this->removed = $removed ? date_create() : null;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @param int|null $count
     *
     * @return Map
     */
    public function setCount(?int $count): Map
    {
        $this->count = $count;
        return $this;
    }

}