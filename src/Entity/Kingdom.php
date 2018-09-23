<?php

namespace App\Entity;

use App\Interfaces\TaxesInterface;
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
     * @ORM\Column(type="integer", options={"default": 1})
     */
    private $level;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
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
    private $metal;

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
    private $onMetal;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $onBuildings;

    /**
     * @ORM\Column(type="smallint", options={"default": 0})
     */
    private $tax;

    /**
     * @ORM\Column(type="datetime", options={"default"="CURRENT_TIMESTAMP"})
     */
    private $grabResourcesDate;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="kingdom", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Build", mappedBy="kingdom", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $builds;

    public function __construct(string $kingdomName, User $user, BuildType $castleType)
    {
        $this->name = $kingdomName;
        $this->user = $user;

        $this->setTax(TaxesInterface::TAXE_MEDIUM);
        $this->setLevel(1);
        $this->setPeople(100);
        $this->setOnFood(40);
        $this->setOnBuildings(20);
        $this->setOnWood(40);
        $this->setOnStone(0);
        $this->setOnMetal(0);
        $this->setFood(1000);
        $this->setWood(1000);
        $this->setStone(0);
        $this->setMetal(0);
        $this->setGold(10);
        $this->setGrabResourcesDate(new \DateTime());
        $this->builds = new ArrayCollection();
        $castle = new Build($castleType, $this, 1);
        $this->addBuild($castle);
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

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     */
    public function setLevel($level): void
    {
        $this->level = $level;
    }

    public function getPeople(): int
    {
        return $this->people;
    }

    public function setPeople(int $people): self
    {
        $this->people = $people;

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

    public function getMetal(): int
    {
        return $this->metal;
    }

    public function setMetal(int $metal): self
    {
        $this->metal = $metal;
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
            throw new \DivisionByZeroError();
        }

        $this->tax = $tax;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGrabResourcesDate()
    {
        return $this->grabResourcesDate;
    }

    public function setGrabResourcesDate($grabResourcesDate): self
    {
        $this->grabResourcesDate = $grabResourcesDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOnFood()
    {
        return $this->onFood;
    }

    /**
     * @param mixed $onFood
     */
    public function setOnFood($onFood): void
    {
        $this->onFood = $onFood;
    }

    /**
     * @return mixed
     */
    public function getOnMetal()
    {
        return $this->onMetal;
    }

    /**
     * @param mixed $onMetal
     */
    public function setOnMetal($onMetal): void
    {
        $this->onMetal = $onMetal;
    }

    /**
     * @return mixed
     */
    public function getOnStone()
    {
        return $this->onStone;
    }

    /**
     * @param mixed $onStone
     */
    public function setOnStone($onStone): void
    {
        $this->onStone = $onStone;
    }

    /**
     * @return mixed
     */
    public function getOnWood()
    {
        return $this->onWood;
    }

    /**
     * @param mixed $onWood
     */
    public function setOnWood($onWood): void
    {
        $this->onWood = $onWood;
    }

    /**
     * @return mixed
     */
    public function getOnBuildings()
    {
        return $this->onBuildings;
    }

    /**
     * @param mixed $onBuildings
     */
    public function setOnBuildings($onBuildings): void
    {
        $this->onBuildings = $onBuildings;
    }

    /**
     * @return Collection|Build[]
     */
    public function getBuilds(): Collection
    {
        return $this->builds;
    }

    /**
     * @param string $buildTypeCode
     * @return Build|null
     */
    public function getBuild(string $buildTypeCode): ?Build
    {
        foreach ($this->getBuilds() as  $build) {
            if ($build->getType()->getCode() === $buildTypeCode) {
                return $build;
            }
        }

        return null;
    }

    /**
     * @param string $buildTypeCode
     * @param Build $value
     * @return self
     */
    public function setBuild(string $buildTypeCode, Build $value): self
    {
        $found = false;
        foreach ($this->getBuilds() as $key => $build) {
            if ($build->getType()->getCode() === $buildTypeCode) {
                $this->builds[$key] = $value;
                $found = true;
            }
        }

        if (!$found) {
            $this->builds->add($value);
        }

        return $this;
    }

    public function addBuild(Build $build): self
    {
        if (!$this->builds->contains($build)) {
            $this->builds[] = $build;
            $build->setKingdom($this);
        }

        return $this;
    }

    public function removeBuild(Build $build): self
    {
        if ($this->builds->contains($build)) {
            $this->builds->removeElement($build);
            // set the owning side to null (unless already changed)
            if ($build->getKingdom() === $this) {
                $build->setKingdom(null);
            }
        }

        return $this;
    }
}
