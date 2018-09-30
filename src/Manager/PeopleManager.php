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
     * @param BotManager $botManager
     * @param KingdomManager $kingdomManager
     */
    public function __construct(BotManager $botManager, KingdomManager $kingdomManager)
    {
        $this->botManager = $botManager;
        $this->kingdomManager = $kingdomManager;
    }

    /**
     * 1 people eat food unit
     * If tax low, people eat lower
     * @return float
     */
    public function eat(): float
    {
        $kingdom = $this->botManager->getKingdom();
        $people = $this->kingdomManager->getPeople();

        return round($people * (ResourceInterface::INITIAL_EAT_FOOD / $kingdom->getTax()));
    }

    /**
     * 1 people pay gold unit
     * If tax big, people pay more
     * @return float
     */
    public function pay(): float
    {
        $kingdom = $this->botManager->getKingdom();
        $people = $this->kingdomManager->getPeople();

        return round($people * (TaxesInterface::INITIAL_TAXES_SIZE * $kingdom->getTax()));
    }
}
