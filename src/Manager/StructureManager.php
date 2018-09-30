<?php

namespace App\Manager;

use App\Entity\Kingdom;
use App\Entity\Structure;
use App\Entity\StructureType;
use App\Interfaces\ResourceInterface;
use App\Interfaces\StructureInterface;

class StructureManager
{
    /** @var BotManager  */
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
     * @param StructureType $buildType
     * @return bool
     */
    public function checkAvailableResourceForBuyStructure(StructureType $buildType): bool
    {
        $kingdom = $this->botManager->getKingdom();
        $freeTerritorySpace = $this->kingdomManager->getTerritorySize()
            - $this->kingdomManager->getStructureCount();

        $result = true;
        foreach (
            [
                ResourceInterface::RESOURCE_GOLD,
                ResourceInterface::RESOURCE_WOOD,
                ResourceInterface::RESOURCE_STONE,
                ResourceInterface::RESOURCE_IRON
            ] as $resourceType
        ) {
            $result = $kingdom->getResource($resourceType)
                    >= $buildType->getResourceCost($resourceType);

            if (!$result) {
                break;
            }
        }

        if (
            $result &&
            !\in_array($buildType->getCode(), [StructureInterface::STRUCTURE_TYPE_TERRITORY], true)
        ) {
            $result = $result && $freeTerritorySpace > 0;
        }

        return $result;
    }

    /**
     * @param Structure $build
     */
    public function processBuyStructure(Structure $build): void
    {
        $kingdom = $this->botManager->getKingdom();
        $structureType = $build->getType();

        foreach (
            [
                ResourceInterface::RESOURCE_GOLD,
                ResourceInterface::RESOURCE_WOOD,
                ResourceInterface::RESOURCE_STONE,
                ResourceInterface::RESOURCE_IRON
            ] as $resourceType
        ) {
            $kingdom->setResource(
                $resourceType,
                $kingdom->getResource($resourceType)
                    - $structureType->getResourceCost($resourceType)
            );
        }

        $build->setLevel($build->getLevel() + 1);
    }
}
