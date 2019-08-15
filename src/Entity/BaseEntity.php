<?php

namespace Betreuteszocken\CsConfig\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Base class for entities.
 *
 * @package Betreuteszocken\CsConfig
 */
abstract class BaseEntity
{

    /**
     * @var integer|null
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id = null;

    /**
     * Static Entity constructor.
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Returns the unique id or null if the entity is not persisted yet.
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}