<?php

namespace App\Manager;

use App\Entity\Structure;
use App\Entity\StructureType;
use App\Interfaces\ResourceInterface;
use App\Interfaces\StructureInterface;

class StructureManager
{
    /** @var BotManager */
    protected $botManager;
    /** @var KingdomManager */
    protected $kingdomManager;

    /**
     * StructureManager constructor.
     */
    public function __construct(BotManager $botManager, KingdomManager $kingdomManager)
    {
        $this->botManager = $botManager;
        $this->kingdomManager = $kingdomManager;
    }

    /**
     * Check.
     */
    public function hasAvailableForSomeStructure(StructureType $buildType, int $count): bool
    {
        $kingdom = $this->botManager->getKingdom();

        $result = true;
        foreach (
            [
                ResourceInterface::RESOURCE_GOLD,
                ResourceInterface::RESOURCE_WOOD,
                ResourceInterface::RESOURCE_STONE,
                ResourceInterface::RESOURCE_IRON,
            ] as $resourceType
        ) {
            $result = $kingdom->getResource($resourceType)
                    >= $buildType->getResourceCost($resourceType) * $count;

            if (!$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * Check.
     */
    public function hasFreeSpaceForSomeStructure(StructureType $buildType, int $count): bool
    {
        $freeTerritorySpace = $this->kingdomManager->getTerritorySize()
            - $this->kingdomManager->getStructureCount();

        if (!\in_array($buildType->getCode(), [StructureInterface::STRUCTURE_TYPE_TERRITORY], true)
        ) {
            $result = $freeTerritorySpace > $count;
        } else {
            $result = true;
        }

        return $result;
    }

    /**
     * Buy.
     */
    public function processBuySomeStructure(Structure $build, int $count): void
    {
        $kingdom = $this->botManager->getKingdom();
        $structureType = $build->getType();

        foreach (
            [
                ResourceInterface::RESOURCE_GOLD,
                ResourceInterface::RESOURCE_WOOD,
                ResourceInterface::RESOURCE_STONE,
                ResourceInterface::RESOURCE_IRON,
            ] as $resourceType
        ) {
            $kingdom->setResource(
                $resourceType,
                $kingdom->getResource($resourceType)
                    - $structureType->getResourceCost($resourceType) * $count
            );
        }

        $build->setLevel($build->getLevel() + $count);
    }
}
