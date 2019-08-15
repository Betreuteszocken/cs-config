<?php

namespace Betreuteszocken\CsConfig\Entity;

use Betreuteszocken\CsConfig\DBAL\LogType;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Class Map
 *
 * @ORM\Entity(repositoryClass="\Betreuteszocken\CsConfig\Repository\LogRepository")
 * @ORM\Table(name="log")
 *
 * @package Betreuteszocken\CsConfig
 */
class Log extends BaseEntity
{
    use ORMBehaviors\Blameable\Blameable,
        ORMBehaviors\Timestampable\Timestampable;

    /**
     *
     * @var int
     *
     * @ORM\Column(name="type", type="log_type")
     */
    protected $type = LogType::TYPE_COMMON;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    protected $message = '';

    /**
     * @return integer
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param integer $type
     *
     * @return Log
     */
    public function setType(int $type): Log
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return Log
     */
    public function setMessage(string $message): Log
    {
        $this->message = $message;
        return $this;
    }
}