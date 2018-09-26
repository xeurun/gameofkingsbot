<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StructureRepository")
 */
class Structure
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
     * @ORM\ManyToOne(targetEntity="App\Entity\StructureType")
     * @ORM\JoinColumn(nullable=false, referencedColumnName="code")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Kingdom", inversedBy="structures")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $kingdom;

    /**
     * @ORM\Column(type="datetime", options={"default"="CURRENT_TIMESTAMP"})
     */
    private $buildEndDate;

    public function __construct(StructureType $buildType, Kingdom $kingdom, int $level)
    {
        $this->kingdom = $kingdom;
        $this->type = $buildType;
        $this->setLevel($level);
        $this->setBuildEndDate(new \DateTime());
    }

    public function setBuildEndDate(\DateTimeInterface $value): self
    {
        $this->buildEndDate = $value;

        return $this;
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

    public function getType(): StructureType
    {
        return $this->type;
    }

    public function getKingdom(): Kingdom
    {
        return $this->kingdom;
    }

    public function getCreatedDate(): \DateTimeInterface
    {
        return $this->buildEndDate;
    }
}
