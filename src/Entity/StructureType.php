<?php

namespace App\Entity;

use App\Interfaces\ResourceInterface;
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

    public function getResourceCost(string $resourceType)
    {
        switch ($resourceType) {
            case ResourceInterface::RESOURCE_GOLD:
                $value = $this->goldCost;

                break;
            case ResourceInterface::RESOURCE_WOOD:
                $value = $this->woodCost;

                break;
            case ResourceInterface::RESOURCE_STONE:
                $value = $this->stoneCost;

                break;
            case ResourceInterface::RESOURCE_IRON:
                $value = $this->ironCost;

                break;
            default:
                throw new \InvalidArgumentException('Undefined resource type!');
        }

        return $value;
    }

    public function getMaxLevel()
    {
        return $this->maxLevel;
    }

    public function setMaxLevel($maxLevel): void
    {
        $this->maxLevel = $maxLevel;
    }
}
