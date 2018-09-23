<?php

namespace App\Manager;

use App\Entity\Kingdom;
use App\Entity\Structure;
use App\Entity\StructureType;
use App\Interfaces\ResourceInterface;
use App\Interfaces\StructureInterface;

class ResourceManager
{
    protected $botManager;
    protected $workManager;
    protected $peopleManager;

    public function __construct(BotManager $botManager, WorkManager $workManager, PeopleManager $peopleManager)
    {
        $this->botManager = $botManager;
        $this->workManager = $workManager;
        $this->peopleManager = $peopleManager;
    }

    public function addEveryDayBonus(Kingdom $kingdom): void
    {
        $kingdom->setFood($kingdom->getFood() + ResourceInterface::EVERY_DAY_FOOD_BONUS);
        $kingdom->setGold($kingdom->getGold() + ResourceInterface::EVERY_DAY_GOLD_BONUS);
        $kingdom->setWood($kingdom->getWood() + ResourceInterface::EVERY_DAY_WOOD_BONUS);
        $kingdom->setStone($kingdom->getStone() + ResourceInterface::EVERY_DAY_STONE_BONUS);
        $kingdom->setIron($kingdom->getIron() + ResourceInterface::EVERY_DAY_IRON_BONUS);
    }

    /**
     * @param Kingdom $kingdom
     */
    public function moveExtractedResourcesToWarehouse(Kingdom $kingdom)
    {
        $today = new \DateTime();

        $extractedGold = $this->getExtractedCountByResourceName(ResourceInterface::RESOURCE_GOLD);
        $extractedFood = $this->getExtractedCountByResourceName(ResourceInterface::RESOURCE_FOOD);
        $extractedWood = $this->getExtractedCountByResourceName(ResourceInterface::RESOURCE_WOOD);
        $extractedStone = $this->getExtractedCountByResourceName(ResourceInterface::RESOURCE_STONE);
        $extractedIron = $this->getExtractedCountByResourceName(ResourceInterface::RESOURCE_IRON);

        $kingdom->setFood($kingdom->getFood() + $extractedFood);
        $kingdom->setGold($kingdom->getGold() + $extractedGold);
        $kingdom->setWood($kingdom->getWood() + $extractedWood);
        $kingdom->setStone($kingdom->getStone() + $extractedStone);
        $kingdom->setIron($kingdom->getIron() + $extractedIron);

        $kingdom->setGrabResourcesDate($today);
    }

    /**
     * Get the extracted resource count
     * @param string $resourceName
     * @return float|int|null
     */
    public function getExtractedCountByResourceName(string $resourceName)
    {
        $kingdom = $this->botManager->getKingdom();
        $goldHourly = $this->peopleManager->pay($kingdom);
        $foodHourly = $this->workManager->food($kingdom);
        $woodHourly = $this->workManager->wood($kingdom);
        $stoneHourly = $this->workManager->stone($kingdom);
        $ironHourly = $this->workManager->iron($kingdom);

        $hours = $this->workManager->workedHours($kingdom);

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
