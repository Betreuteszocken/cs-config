<?php

namespace Betreuteszocken\CsConfig\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 *
 * @ORM\Entity(repositoryClass="\Betreuteszocken\CsConfig\Repository\CycleConfigRepository")
 * @ORM\Table(name="cycle_config")
 *
 * @package Betreuteszocken\CsConfig
 */
class CycleConfig extends BaseEntity
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var Map[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="\Betreuteszocken\CsConfig\Entity\Map")
     * @ORM\JoinTable(name="cycle_config_mami_maps",
     *     joinColumns={@ORM\JoinColumn(name="map_cycle_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="map_id", referencedColumnName="id")}
     * )
     */
    protected $mamiMaps = null;

    /**
     * amount of user selected maps
     *
     * @var integer
     *
     * @ORM\Column(name="user_maps", type="smallint", options={"unsigned":true})
     */
    protected $userMaps = 0;

    /**
     * amount of pre-chosen maps
     *
     * @var integer
     *
     * @ORM\Column(name="default_maps", type="smallint", options={"unsigned":true})
     */
    protected $defaultMaps = 0;

    /**
     * amount of maps of pre-chosen categories
     *
     * @var integer
     *
     * @ORM\Column(name="default_category_maps", type="smallint", options={"unsigned":true})
     */
    protected $defaultCategoryMaps = 0;

    /**
     * amount of origin cs1.6 maps
     *
     * @var integer
     *
     * @ORM\Column(name="origin_maps", type="smallint", options={"unsigned":true})
     */
    protected $originMaps = 0;

    /**
     * amount of random maps
     *
     * @var integer
     *
     * @ORM\Column(name="random_maps", type="smallint", options={"unsigned":true})
     */
    protected $randomMaps = 0;

    /**
     * MapCycle constructor.
     */
    public function __construct()
    {
        $this->mamiMaps = new ArrayCollection();
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
     * @return CycleConfig
     */
    public function setMamiMaps($maps): CycleConfig
    {
        $this->mamiMaps = is_object($maps) ? $maps : (new ArrayCollection($maps));
        return $this;
    }

    /**
     * @return int
     */
    public function getUserMaps(): int
    {
        return $this->userMaps;
    }

    /**
     * @param int $count
     *
     * @return CycleConfig
     */
    public function setUserMaps(int $count): CycleConfig
    {
        $this->userMaps = $count;
        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultMaps(): int
    {
        return $this->defaultMaps;
    }

    /**
     * @param int $count
     *
     * @return CycleConfig
     */
    public function setDefaultMaps(int $count): CycleConfig
    {
        $this->defaultMaps = $count;
        return $this;
    }


    /**
     * @return integer
     */
    public function getDefaultCategoryMaps(): int
    {
        return $this->defaultCategoryMaps;
    }

    /**
     * @param integer $count
     *
     * @return CycleConfig
     */
    public function setDefaultCategoryMaps(int $count): CycleConfig
    {
        $this->defaultCategoryMaps = $count;
        return $this;
    }

    /**
     * @return int
     */
    public function getOriginMaps(): int
    {
        return $this->originMaps;
    }

    /**
     * @param int $count
     *
     * @return CycleConfig
     */
    public function setOriginMaps(int $count): CycleConfig
    {
        $this->originMaps = $count;
        return $this;
    }

    /**
     * @return int
     */
    public function getRandomMaps(): int
    {
        return $this->randomMaps;
    }

    /**
     * @param int $count
     *
     * @return CycleConfig
     */
    public function setRandomMaps(int $count): CycleConfig
    {
        $this->randomMaps = $count;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalMaps(): int
    {
        return $this->mamiMaps->count()
            + $this->userMaps
            + $this->defaultMaps
            + $this->defaultCategoryMaps
            + $this->originMaps
            + $this->randomMaps;
    }

}