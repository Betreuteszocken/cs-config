<?php

namespace Betreuteszocken\CsConfig\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="\Betreuteszocken\CsConfig\Repository\CycleRepository")
 * @ORM\Table(name="cycle")
 *
 * @package Betreuteszocken\CsConfig
 */
class Cycle extends BaseEntity
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var CycleConfig
     *
     * @ORM\OneToOne(targetEntity="\Betreuteszocken\CsConfig\Entity\CycleConfig", cascade={"persist"})
     * @ORM\JoinColumn(name="cycle_config_id", referencedColumnName="id")
     */
    protected $config = null;

    /**
     * @var Map[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="\Betreuteszocken\CsConfig\Entity\Map", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="cycle_maps_mami",
     *     joinColumns={@ORM\JoinColumn(name="cycle_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="map_id", referencedColumnName="id")}
     * )
     */
    protected $mamiMaps = null;

    /**
     * @var Map[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="\Betreuteszocken\CsConfig\Entity\Map", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="cycle_maps_user",
     *     joinColumns={@ORM\JoinColumn(name="cycle_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="map_id", referencedColumnName="id")}
     * )
     */
    protected $userMaps = null;

    /**
     * @var Map[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="\Betreuteszocken\CsConfig\Entity\Map", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="cycle_maps_default",
     *     joinColumns={@ORM\JoinColumn(name="cycle_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="map_id", referencedColumnName="id")}
     * )
     */
    protected $defaultMaps = null;

    /**
     * @var Map[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="\Betreuteszocken\CsConfig\Entity\Map", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="cycle_maps_default_category",
     *     joinColumns={@ORM\JoinColumn(name="cycle_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="map_id", referencedColumnName="id")}
     * )
     */
    protected $defaultCategoryMaps = null;

    /**
     * @var Map[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="\Betreuteszocken\CsConfig\Entity\Map", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="cycle_maps_origin",
     *     joinColumns={@ORM\JoinColumn(name="cycle_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="map_id", referencedColumnName="id")}
     * )
     */
    protected $originMaps = null;

    /**
     * @var Map[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="\Betreuteszocken\CsConfig\Entity\Map", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="cycle_maps_random",
     *     joinColumns={@ORM\JoinColumn(name="cycle_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="map_id", referencedColumnName="id")}
     * )
     */
    protected $randomMaps = null;

    /**
     * @var string
     *
     * @ORM\Column(name="mapcycle_txt", type="text")
     */
    protected $mapcycleTxt = '';

    /**
     * `true` if this map cycle is the current running one, else `false`
     *
     * @var boolean
     */
    protected $current = false;

    /**
     * MapCycle constructor.
     */
    public function __construct()
    {
        $this->config              = new CycleConfig();
        $this->mamiMaps            = new ArrayCollection();
        $this->userMaps            = new ArrayCollection();
        $this->defaultMaps         = new ArrayCollection();
        $this->defaultCategoryMaps = new ArrayCollection();
        $this->originMaps          = new ArrayCollection();
        $this->randomMaps          = new ArrayCollection();
    }

    /**
     * @return CycleConfig
     */
    public function getConfig(): CycleConfig
    {
        return $this->config;
    }

    /**
     * @param CycleConfig $config
     *
     * @return Cycle
     */
    public function setConfig(CycleConfig $config): Cycle
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return Map[]|Collection
     */
    public function getMamiMaps(): Collection
    {
        return $this->mamiMaps;
    }

    /**
     * @param Map[]|Collection $maps
     *
     * @return Cycle
     */
    public function setMamiMaps($maps): Cycle
    {
        $this->mamiMaps = is_object($maps) ? $maps : (new ArrayCollection($maps));
        return $this;
    }

    /**
     * @param Map $map
     *
     * @return Cycle
     */
    public function addMamiMap(Map $map): Cycle
    {
        $this->mamiMaps->add($map);
        return $this;
    }

    /**
     * @return Map[]|Collection
     */
    public function getUserMaps(): Collection
    {
        return $this->userMaps;
    }

    /**
     * @param Map[]|Collection $maps
     *
     * @return Cycle
     */
    public function setUserMaps($maps): Cycle
    {
        $this->userMaps = is_object($maps) ? $maps : (new ArrayCollection($maps));;
        return $this;
    }

    /**
     * @param Map $map
     *
     * @return Cycle
     */
    public function addUserMap(Map $map): Cycle
    {
        $this->userMaps->add($map);
        return $this;
    }

    /**
     * @return Map[]|Collection
     */
    public function getDefaultMaps(): Collection
    {
        return $this->defaultMaps;
    }

    /**
     * @param Map[]|Collection $maps
     *
     * @return Cycle
     */
    public function setDefaultMaps($maps): Cycle
    {
        $this->defaultMaps = is_object($maps) ? $maps : (new ArrayCollection($maps));;
        return $this;
    }

    /**
     * @param Map $map
     *
     * @return Cycle
     */
    public function addDefaultMap(Map $map): Cycle
    {
        $this->defaultMaps->add($map);
        return $this;
    }

    /**
     * @return Map[]|Collection
     */
    public function getDefaultCategoryMaps(): Collection
    {
        return $this->defaultCategoryMaps;
    }

    /**
     * @param Map[]|Collection $maps
     *
     * @return Cycle
     */
    public function setDefaultCategoryMaps($maps): Cycle
    {
        $this->defaultCategoryMaps = is_object($maps) ? $maps : (new ArrayCollection($maps));
        return $this;
    }

    /**
     * @param Map $map
     *
     * @return Cycle
     */
    public function addDefaultCategoryMap(Map $map): Cycle
    {
        $this->defaultCategoryMaps->add($map);
        return $this;
    }


    /**
     * @return Map[]|Collection
     */
    public function getOriginMaps(): Collection
    {
        return $this->originMaps;
    }

    /**
     * @param Map[]|Collection $maps
     *
     * @return Cycle
     */
    public function setOriginMaps($maps): Cycle
    {
        $this->originMaps = is_object($maps) ? $maps : (new ArrayCollection($maps));;
        return $this;
    }

    /**
     * @param Map $map
     *
     * @return Cycle
     */
    public function addOriginMap(Map $map): Cycle
    {
        $this->originMaps->add($map);
        return $this;
    }

    /**
     * @return Map[]|Collection
     */
    public function getRandomMaps(): Collection
    {
        return $this->randomMaps;
    }

    /**
     * @param Map[]|Collection $maps
     *
     * @return Cycle
     */
    public function setRandomMaps($maps): Cycle
    {
        $this->randomMaps = is_object($maps) ? $maps : (new ArrayCollection($maps));
        return $this;
    }

    /**
     * @param Map $map
     *
     * @return Cycle
     */
    public function addRandomMap(Map $map): Cycle
    {
        $this->randomMaps->add($map);
        return $this;
    }

    /**
     * @return string
     */
    public function getMapcycleTxt(): string
    {
        return $this->mapcycleTxt;
    }

    /**
     * @param string $mapcycleTxt
     *
     * @return Cycle
     */
    public function setMapcycleTxt(string $mapcycleTxt): Cycle
    {
        $this->mapcycleTxt = $mapcycleTxt;
        return $this;
    }


//    /**
//     *
//     * @return string
//     */
//    public function getHash(): string
//    {
//        return md5(implode('|', array_map(function (Map $_map) {
//            return $_map->getName();
//        }, $this->getMaps())));
//    }

    /**
     * Returns all maps which are chosen for this map-cycle.
     *
     * @return Map[]
     */
    public function getAllMaps(): array
    {
        $maps = array();

        $maps = array_merge($maps, $this->mamiMaps->toArray());
        $maps = array_merge($maps, $this->userMaps->toArray());
        $maps = array_merge($maps, $this->defaultMaps->toArray());
        $maps = array_merge($maps, $this->defaultCategoryMaps->toArray());
        $maps = array_merge($maps, $this->originMaps->toArray());
        $maps = array_merge($maps, $this->randomMaps->toArray());

        return $maps;
    }

    /**
     * Returns all maps which are chosen for this map-cycle.
     *
     * @return integer
     */
    public function getTotalMapCount(): int
    {
        return 0
            + $this->mamiMaps->count()
            + $this->userMaps->count()
            + $this->defaultMaps->count()
            + $this->defaultCategoryMaps->count()
            + $this->originMaps->count()
            + $this->randomMaps->count();
    }

    /**
     * @param Map $map
     *
     * @return bool
     */
    public function containsMap(Map $map): bool
    {
        $mapNames = array_map(function (Map $_map) {
            return $_map->getName();
        }, $this->getAllMaps());

        return in_array($map->getName(), $mapNames);
    }

    /**
     * Return `true` if this map cycle is the current running one, else `false`.
     *
     * @return boolean
     */
    public function isCurrent(): bool
    {
        return $this->current;
    }

    /**
     * Pass `true` if this map cycle is the current running one, else `false`.
     *
     * @param boolean $current
     *
     * @return Cycle
     */
    public function setCurrent(bool $current): Cycle
    {
        $this->current = $current;
        return $this;
    }

}