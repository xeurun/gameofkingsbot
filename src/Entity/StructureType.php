<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StructureTypeRepository")
 */
class StructureType
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", unique=true, length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $order;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default": 0})
     */
    private $maxLevel;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $goldCost;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $woodCost;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $stoneCost;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $ironCost;

    /**
     * @param string $structureCode
     */
    public function __construct(string $structureCode)
    {
        $this->code = $structureCode;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWoodCost(): int
    {
        return $this->woodCost;
    }

    public function setWoodCost(int $value): self
    {
        $this->woodCost = $value;

        return $this;
    }

    public function getGoldCost(): int
    {
        return $this->goldCost;
    }

    public function setGoldCost(int $value): self
    {
        $this->goldCost = $value;

        return $this;
    }

    public function getStoneCost(): int
    {
        return $this->stoneCost;
    }

    public function setStoneCost(int $value): self
    {
        $this->stoneCost = $value;

        return $this;
    }

    public function getIronCost(): int
    {
        return $this->ironCost;
    }

    public function setIronCost(int $value): self
    {
        $this->ironCost = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxLevel()
    {
        return $this->maxLevel;
    }

    /**
     * @param mixed $maxLevel
     */
    public function setMaxLevel($maxLevel): void
    {
        $this->maxLevel = $maxLevel;
    }
}
