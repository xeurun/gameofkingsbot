<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BuildTypeRepository")
 */
class BuildType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", unique=true, length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $gold;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $wood;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $stone;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $metal;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $time;

    public function getId(): int
    {
        return $this->id;
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getWood(): int
    {
        return $this->wood;
    }

    public function setWood(int $wood): self
    {
        $this->wood = $wood;

        return $this;
    }

    public function getGold(): int
    {
        return $this->gold;
    }

    public function setGold(int $gold): self
    {
        $this->gold = $gold;

        return $this;
    }

    public function getStone(): int
    {
        return $this->stone;
    }

    public function setStone(int $stone): self
    {
        $this->stone = $stone;

        return $this;
    }

    public function getMetal(): int
    {
        return $this->metal;
    }

    public function setMetal(int $metal): self
    {
        $this->metal = $metal;

        return $this;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }
}
