<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BuildRepository")
 */
class Build
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BuildType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Kingdom", inversedBy="builds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $kingdom;

    /**
     * @ORM\Column(type="datetime", options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdDate;

    public function __construct(BuildType $buildType, Kingdom $kingdom, int $level)
    {
        $this->kingdom = $kingdom;
        $this->type = $buildType;
        $this->setLevel($level);
        $this->setCreatedDate(new \DateTime());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getType(): BuildType
    {
        return $this->type;
    }

    public function getKingdom(): Kingdom
    {
        return $this->kingdom;
    }

    public function getCreatedDate(): \DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }
}
