<?php

namespace App\Manager;

use App\Interfaces\ResourceInterface;
use App\Interfaces\WorkInterface;

class ResourceManager
{
    /** @var BotManager */
    protected $botManager;
    /** @var WorkManager */
    protected $workManager;
    /** @var PeopleManager */
    protected $peopleManager;
    /** @var KingdomManager */
    protected $kingdomManager;

    /**
     * ResourceManager constructor.
     */
    public function __construct(
        BotManager $botManager,
        WorkManager $workManager,
        PeopleManager $peopleManager,
        KingdomManager $kingdomManager
    ) {
        $this->botManager = $botManager;
        $this->workManager = $workManager;
        $this->peopleManager = $peopleManager;
        $this->kingdomManager = $kingdomManager;
    }

    /**
     * Add every day bonus
     */
    public function addEveryDayBonus(): void
    {
        $kingdom = $this->botManager->getKingdom();
        $bonus = [
            ResourceInterface::RESOURCE_GOLD => ResourceInterface::EVERY_DAY_GOLD_BONUS,
            ResourceInterface::RESOURCE_FOOD => ResourceInterface::EVERY_DAY_FOOD_BONUS,
            ResourceInterface::RESOURCE_WOOD => ResourceInterface::EVERY_DAY_WOOD_BONUS,
            ResourceInterface::RESOURCE_STONE => ResourceInterface::EVERY_DAY_STONE_BONUS,
            ResourceInterface::RESOURCE_IRON => ResourceInterface::EVERY_DAY_IRON_BONUS,
        ];

        foreach ($bonus as $resourceType => $bonusValue) {
            $kingdom->setResource(
                $resourceType,
                $kingdom->getResource($resourceType) + $bonusValue
            );
        }
    }

    /**
     * Move res to warehouse
     */
    public function moveExtractedResourcesToWarehouse(): void
    {
        $kingdom = $this->botManager->getKingdom();
        $today = new \DateTime();

        $extractedGold = $this->getExtractedCountByResourceName(ResourceInterface::RESOURCE_GOLD);
        $extractedFood = $this->getExtractedCountByResourceName(ResourceInterface::RESOURCE_FOOD);
        $extractedWood = $this->getExtractedCountByResourceName(ResourceInterface::RESOURCE_WOOD);
        $extractedStone = $this->getExtractedCountByResourceName(ResourceInterface::RESOURCE_STONE);
        $extractedIron = $this->getExtractedCountByResourceName(ResourceInterface::RESOURCE_IRON);

        $resources = [
            ResourceInterface::RESOURCE_GOLD => $extractedGold,
            ResourceInterface::RESOURCE_FOOD => $extractedFood,
            ResourceInterface::RESOURCE_WOOD => $extractedWood,
            ResourceInterface::RESOURCE_STONE => $extractedStone,
            ResourceInterface::RESOURCE_IRON => $extractedIron,
        ];

        foreach ($resources as $resourceType => $resourceValue) {
            $max = $this->kingdomManager->getMax($resourceType);
            $newValue = $kingdom->getResource($resourceType) + $resourceValue;

            if ($newValue > $max) {
                $kingdom->setResource(
                    $resourceType,
                    $max
                );
            } else {
                $kingdom->setResource(
                    $resourceType,
                    $newValue
                );
            }
        }

        $kingdom->setGrabResourcesDate($today);
    }

    /**
     * Get the extracted resource count.
     *
     * @return float|int|null
     */
    public function getExtractedCountByResourceName(string $resourceName)
    {
        $goldHourly = $this->peopleManager->pay();
        $foodHourly = $this->workManager->getSalary(WorkInterface::WORK_TYPE_FOOD);
        $woodHourly = $this->workManager->getSalary(WorkInterface::WORK_TYPE_WOOD);
        $stoneHourly = $this->workManager->getSalary(WorkInterface::WORK_TYPE_STONE);
        $ironHourly = $this->workManager->getSalary(WorkInterface::WORK_TYPE_IRON);

        $hours = $this->workManager->workedHours();

        $stack = null;
        switch ($resourceName) {
            case ResourceInterface::RESOURCE_GOLD:
                $stack = $goldHourly * $hours;

                break;
            case ResourceInterface::RESOURCE_FOOD:
                $stack = $foodHourly * $hours;

                break;
            case ResourceInterface::RESOURCE_WOOD:
                $stack = $woodHourly * $hours;

                break;
            case ResourceInterface::RESOURCE_STONE:
                $stack = $stoneHourly * $hours;

                break;
            case ResourceInterface::RESOURCE_IRON:
                $stack = $ironHourly * $hours;

                break;
            default:
                break;
        }

        return $stack;
    }
}
