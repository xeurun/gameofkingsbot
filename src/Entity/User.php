<?php

namespace App\Entity;

use App\Interfaces\StateInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /** @var string */
    public const AVAILABLE_LANG_RU = 'ru';
    /** @var string */
    public const AVAILABLE_GENDER_KING = 'king';
    /** @var string */
    public const AVAILABLE_GENDER_QUEEN = 'queen';

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true, length=2)
     */
    private $lang;

    /**
     * @ORM\Column(type="string", nullable=true, length=5)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $bonusDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Kingdom", mappedBy="user", orphanRemoval=true)
     */
    private $kingdom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="mainRefer")
     */
    private $refers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="childRefer")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $refer;

    public function __construct(\Longman\TelegramBot\Entities\User $user)
    {
        $this->id = $user->getId();
        $this->setLang(self::AVAILABLE_LANG_RU);
        $this->username = $user->getUsername();
        $this->name = $user->getFirstName();
        $this->setState(StateInterface::STATE_WAIT_CHOOSE_GENDER);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    public function setLang(?string $value): self
    {
        $this->lang = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    public function setGender(?string $value): self
    {
        $this->gender = $value;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getKingdom(): ?Kingdom
    {
        return $this->kingdom;
    }

    public function setKingdom(Kingdom $kingdom): self
    {
        $this->kingdom = $kingdom;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getBonusDate(): ?\DateTimeInterface
    {
        return $this->bonusDate;
    }

    public function setBonusDate($bonusDate): self
    {
        $this->bonusDate = $bonusDate;
        return $this;
    }

    /**
     * @param User|null $value
     */
    public function setRefer(?User $value): void
    {
        $this->refer = $value;
    }

    /**
     * @return mixed
     */
    public function getRefer()
    {
        return $this->refer;
    }
}
