<?php

namespace App\Entity;

use App\Interfaces\StateInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lang;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $last_name;

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
     * @ORM\OneToOne(targetEntity="App\Entity\Kingdom", mappedBy="user", cascade={"persist", "remove"})
     */
    private $kingdom;

    public function __construct(\Longman\TelegramBot\Entities\User $user)
    {
        $this->id = $user->getId();
        $this->lang = $user->getLanguageCode();
        $this->username = $user->getUsername();
        $this->first_name = $user->getFirstName();
        $this->last_name = $user->getLastName();
        $this->state = StateInterface::STATE_NEW_PLAYER;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state): self
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
     * @return mixed
     */
    public function getBonusDate()
    {
        return $this->bonusDate;
    }

    public function setBonusDate($bonusDate): self
    {
        $this->bonusDate = $bonusDate;
        return $this;
    }
}