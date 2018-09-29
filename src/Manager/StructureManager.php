<?php

namespace App\Manager;

use App\Entity\Kingdom;
use App\Entity\Structure;
use App\Entity\StructureType;

class StructureManager
{
    /** @var BotManager  */
    protected $botManager;
    /** @var KingdomManager */
    protected $kingdomManager;

    public function __construct(BotManager $botManager, KingdomManager $kingdomManager)
    {
        $this->botManager = $botManager;
        $this->kingdomManager = $kingdomManager;
    }

    public function checkAvailableResourceForBuyStructure(Kingdom $kingdom, StructureType $buildType)
    {
        $freeTerritorySpace = $this->kingdomManager->getTerritorySize($kingdom)
            - $this->kingdomManager->getStructureCount($kingdom);

        return $kingdom->getGold() >= $buildType->getGoldCost() &&
            $kingdom->getWood() >= $buildType->getWoodCost() &&
            $kingdom->getStone() >= $buildType->getStoneCost() &&
            $kingdom->getIron() >= $buildType->getIronCost() && $freeTerritorySpace > 0;
    }

    public function processBuyStructure(Kingdom $kingdom, Structure $build)
    {
        $structureType = $build->getType();
        $kingdom->setGold($kingdom->getGold() - $structureType->getGoldCost());
        $kingdom->setWood($kingdom->getWood() - $structureType->getWoodCost());
        $kingdom->setStone($kingdom->getStone() - $structureType->getStoneCost());
        $kingdom->setIron($kingdom->getIron() - $structureType->getIronCost());

        $build->setLevel($build->getLevel() + 1);

    }
}
