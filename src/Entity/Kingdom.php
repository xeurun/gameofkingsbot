<?php

namespace App\Entity;

use App\Interfaces\AdviserInterface;
use App\Interfaces\ResourceInterface;
use App\Interfaces\TaxesInterface;
use App\Interfaces\WorkInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\KingdomRepository")
 */
class Kingdom
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
     * @ORM\Column(type="integer", options={"default": 100})
     */
    private $people;
    /**
     * @ORM\Column(type="float", precision=2, options={"default": 0})
     */
    private $food;
    /**
     * @ORM\Column(type="float", precision=2, options={"default": 0})
     */
    private $wood;
    /**
     * @ORM\Column(type="float", precision=2, options={"default": 0})
     */
    private $gold;
    /**
     * @ORM\Column(type="float", precision=2, options={"default": 0})
     */
    private $stone;
    /**
     * @ORM\Column(type="float", precision=2, options={"default": 0})
     */
    private $iron;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $onArmy;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $onFood;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $onWood;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $onStone;
    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $onIron;
    /**
     * @ORM\Column(type="smallint", options={"default": 0})
     */
    private $tax;
    /**
     * @ORM\Column(type="datetime", options={"default"="CURRENT_TIMESTAMP"})
     */
    private $grabResourcesDate;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adviserState;
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="kingdom")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Structure", mappedBy="kingdom", cascade={"persist"}, orphanRemoval=true)
     */
    private $structures;

    /**
     * Kingdom
     */
    public function __construct(string $kingdomName, User $user)
    {
        $this->name = $kingdomName;
        $this->user = $user;

        $this->setResource(ResourceInterface::RESOURCE_FOOD, ResourceInterface::INITIAL_FOOD);
        $this->setResource(ResourceInterface::RESOURCE_WOOD, ResourceInterface::INITIAL_WOOD);
        $this->setResource(ResourceInterface::RESOURCE_STONE, ResourceInterface::INITIAL_STONE);
        $this->setResource(ResourceInterface::RESOURCE_IRON, ResourceInterface::INITIAL_IRON);
        $this->setResource(ResourceInterface::RESOURCE_GOLD, ResourceInterface::INITIAL_GOLD);

        $this->setTax(TaxesInterface::INITIAL_TAXES_LEVEL);

        $this->setWorkerCount(WorkInterface::WORK_TYPE_ARMY, WorkInterface::INITIAL_ON_ARMY);
        $this->setWorkerCount(WorkInterface::WORK_TYPE_FOOD, WorkInterface::INITIAL_ON_FOOD);
        $this->setWorkerCount(WorkInterface::WORK_TYPE_WOOD, WorkInterface::INITIAL_ON_WOOD);
        $this->setWorkerCount(WorkInterface::WORK_TYPE_STONE, WorkInterface::INITIAL_ON_STONE);
        $this->setWorkerCount(WorkInterface::WORK_TYPE_IRON, WorkInterface::INITIAL_ON_IRON);

        $this->setGrabResourcesDate(new \DateTime('-1 hour'));
        $this->setAdviserState(AdviserInterface::ADVISER_SHOW_INITIAL_TUTORIAL);

        $this->structures = new ArrayCollection();
    }

    /**
     * Add structure
     */
    public function addStructure(Structure $structure): self
    {
        if (!$this->structures->contains($structure)) {
            $this->structures[] = $structure;
        }

        return $this;
    }

    /**
     * Get
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Change
     */
    public function changeName(string $value): self
    {
        $this->name = $value;

        return $this;
    }

    /**
     * Get
     * @return mixed
     */
    public function getResource(string $resourceType)
    {
        switch ($resourceType) {
            case ResourceInterface::RESOURCE_PEOPLE:
                $value = $this->people;

                break;
            case ResourceInterface::RESOURCE_GOLD:
                $value = $this->gold;

                break;
            case ResourceInterface::RESOURCE_FOOD:
                $value = $this->food;

                break;
            case ResourceInterface::RESOURCE_WOOD:
                $value = $this->wood;

                break;
            case ResourceInterface::RESOURCE_STONE:
                $value = $this->stone;

                break;
            case ResourceInterface::RESOURCE_IRON:
                $value = $this->iron;

                break;
            default:
                throw new \InvalidArgumentException('Undefined resource type: ' . $resourceType);
        }

        return $value;
    }

    /**
     * Set
     * @param mixed $value
     */
    public function setResource(string $resourceType, $value): self
    {
        switch ($resourceType) {
            case ResourceInterface::RESOURCE_PEOPLE:
                $this->people = $value;

                break;
            case ResourceInterface::RESOURCE_GOLD:
                $this->gold = $value;

                break;
            case ResourceInterface::RESOURCE_FOOD:
                $this->food = $value;

                break;
            case ResourceInterface::RESOURCE_WOOD:
                $this->wood = $value;

                break;
            case ResourceInterface::RESOURCE_STONE:
                $this->stone = $value;

                break;
            case ResourceInterface::RESOURCE_IRON:
                $this->iron = $value;

                break;
            default:
                throw new \InvalidArgumentException('Undefined resource type: ' . $resourceType);
        }

        return $this;
    }

    /**
     * Get
     */
    public function getWorkerCount(string $workType): int
    {
        switch ($workType) {
            case WorkInterface::WORK_TYPE_FOOD:
                $value = $this->onFood;

                break;
            case WorkInterface::WORK_TYPE_WOOD:
                $value = $this->onWood;

                break;
            case WorkInterface::WORK_TYPE_STONE:
                $value = $this->onStone;

                break;
            case WorkInterface::WORK_TYPE_IRON:
                $value = $this->onIron;

                break;
            case WorkInterface::WORK_TYPE_ARMY:
                $value = $this->onArmy;

                break;
            default:
                throw new \InvalidArgumentException('Undefined resource type: ' . $workType);
        }

        return $value;
    }

    /**
     * Set
     */
    public function setWorkerCount(string $workType, int $value): self
    {
        switch ($workType) {
            case WorkInterface::WORK_TYPE_FOOD:
                $this->onFood = $value;

                break;
            case WorkInterface::WORK_TYPE_WOOD:
                $this->onWood = $value;

                break;
            case WorkInterface::WORK_TYPE_STONE:
                $this->onStone = $value;

                break;
            case WorkInterface::WORK_TYPE_IRON:
                $this->onIron = $value;

                break;
            case WorkInterface::WORK_TYPE_ARMY:
                $this->onArmy = $value;

                break;
            default:
                throw new \InvalidArgumentException('Undefined work type: ' . $workType);
        }

        return $this;
    }

    /**
     * Get
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get
     */
    public function getTax(): int
    {
        return $this->tax;
    }

    /**
     * Set
     */
    public function setTax($tax): self
    {
        if ($tax <= 0) {
            throw new \DivisionByZeroError('Taxes lower then zero be avoid');
        }

        $this->tax = $tax;

        return $this;
    }

    /**
     * Get
     */
    public function getAdviserState()
    {
        return $this->adviserState;
    }

    /**
     * Set
     */
    public function setAdviserState($adviserState): void
    {
        $this->adviserState = $adviserState;
    }

    /**
     * Get
     */
    public function getGrabResourcesDate(): \DateTimeInterface
    {
        return $this->grabResourcesDate;
    }

    /**
     * Set
     */
    public function setGrabResourcesDate($grabResourcesDate): self
    {
        $this->grabResourcesDate = $grabResourcesDate;

        return $this;
    }

    /**
     * Get
     */
    public function getStructure(string $structureTypeCode): ?Structure
    {
        foreach ($this->getStructures() as $structure) {
            if ($structure->getType()->getCode() === $structureTypeCode) {
                return $structure;
            }
        }

        return null;
    }

    /**
     * @return Collection|Structure[]
     */
    public function getStructures(): Collection
    {
        return $this->structures;
    }

    /**
     * Remove
     */
    public function removeStructure(Structure $structure): self
    {
        if ($this->structures->contains($structure)) {
            $this->structures->removeElement($structure);
        }

        return $this;
    }
}
