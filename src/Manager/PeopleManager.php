<?php

namespace App\Manager;

use App\Interfaces\ResourceInterface;
use App\Interfaces\TaxesInterface;

class PeopleManager
{
    /** @var BotManager */
    protected $botManager;
    /** @var KingdomManager */
    protected $kingdomManager;

    /**
     * PeopleManager constructor.
     */
    public function __construct(BotManager $botManager, KingdomManager $kingdomManager)
    {
        $this->botManager = $botManager;
        $this->kingdomManager = $kingdomManager;
    }

    /**
     * 1 people eat food unit
     * If tax low, people eat lower.
     */
    public function eat(int $hour = 1): float
    {
        $kingdom = $this->botManager->getKingdom();
        $people = $this->kingdomManager->getPeople();

        return round($people * (ResourceInterface::INITIAL_EAT_FOOD / $kingdom->getTax())) * $hour;
    }

    /**
     * 1 people pay gold unit
     * If tax big, people pay more.
     */
    public function pay(): float
    {
        $kingdom = $this->botManager->getKingdom();
        $people = $this->kingdomManager->getPeople();

        return round($people * (TaxesInterface::INITIAL_TAXES_SIZE * $kingdom->getTax()));
    }
}
