<?php

namespace App\Entity;

use App\Interfaces\ResourceInterface;
use App\Interfaces\StructureInterface;
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
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="kingdom")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Structure", mappedBy="kingdom", cascade={"persist"}, orphanRemoval=true)
     */
    private $structures;

    public function __construct(string $kingdomName, User $user)
    {
        $this->name = $kingdomName;
        $this->user = $user;

        $this->setFood(ResourceInterface::INITIAL_FOOD);
        $this->setWood(ResourceInterface::INITIAL_WOOD);
        $this->setStone(ResourceInterface::INITIAL_STONE);
        $this->setIron(ResourceInterface::INITIAL_IRON);
        $this->setGold(ResourceInterface::INITIAL_GOLD);

        $this->setTax(TaxesInterface::INITIAL_TAXES_LEVEL);

        $this->setOnArmy(WorkInterface::INITIAL_ON_ARMY);
        $this->setOnFood(WorkInterface::INITIAL_ON_FOOD);
        $this->setOnWood(WorkInterface::INITIAL_ON_WOOD);
        $this->setOnStone(WorkInterface::INITIAL_ON_STONE);
        $this->setOnIron(WorkInterface::INITIAL_ON_IRON);

        $this->setGrabResourcesDate(new \DateTime('yesterday'));

        $this->structures = new ArrayCollection();
    }

    public function addStructure(Structure $structure): self
    {
        if (!$this->structures->contains($structure)) {
            $this->structures[] = $structure;
        }

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $value): self
    {
        $this->name = $value;
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

    public function getFood(): int
    {
        return $this->food;
    }

    public function setFood(int $value): self
    {
        $this->food = $value;

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

    public function getIron(): int
    {
        return $this->iron;
    }

    public function setIron(int $value): self
    {
        $this->iron = $value;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getTax(): int
    {
        return $this->tax;
    }

    /**
     * @param mixed $tax
     * @return self
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
     * @return mixed
     */
    public function getOnArmy()
    {
        return $this->onArmy;
    }

    /**
     * @param mixed $onArmy
     */
    public function setOnArmy($onArmy): void
    {
        $this->onArmy = $onArmy;
    }

    /**
     * @return int
     */
    public function getOnFood(): int
    {
        return $this->onFood;
    }

    /**
     * @param int $onFood
     */
    public function setOnFood(int $onFood): void
    {
        $this->onFood = $onFood;
    }

    /**
     * @return int
     */
    public function getOnIron(): int
    {
        return $this->onIron;
    }

    /**
     * @param int $onIron
     */
    public function setOnIron(int $onIron): void
    {
        $this->onIron = $onIron;
    }

    /**
     * @return mixed
     */
    public function getOnStone(): int
    {
        return $this->onStone;
    }

    /**
     * @param int $onStone
     */
    public function setOnStone(int $onStone): void
    {
        $this->onStone = $onStone;
    }

    /**
     * @return int
     */
    public function getOnWood(): int
    {
        return $this->onWood;
    }

    /**
     * @param int $onWood
     */
    public function setOnWood(int $onWood): void
    {
        $this->onWood = $onWood;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getGrabResourcesDate(): \DateTimeInterface
    {
        return $this->grabResourcesDate;
    }

    public function setGrabResourcesDate($grabResourcesDate): self
    {
        $this->grabResourcesDate = $grabResourcesDate;
        return $this;
    }

    /**
     * @param string $structureTypeCode
     * @return Structure|null
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

    public function removeStructure(Structure $structure): self
    {
        if ($this->structures->contains($structure)) {
            $this->structures->removeElement($structure);
        }

        return $this;
    }
}
