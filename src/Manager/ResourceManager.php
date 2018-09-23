<?php

namespace App\Manager;

use App\Entity\BuildType;
use App\Entity\Kingdom;
use App\Interfaces\ResourceInterface;

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

    public function checkAvailableResourceForBuyBuild(Kingdom $kingdom, BuildType $buildType)
    {
        return $kingdom->getGold() >= $buildType->getGold() &&
            $kingdom->getWood() >= $buildType->getWood() &&
            $kingdom->getStone() >= $buildType->getStone() &&
            $kingdom->getMetal() >= $buildType->getMetal();
    }

    public function processBuyBuild(Kingdom $kingdom, BuildType $buildType)
    {
        $kingdom->setGold($kingdom->getGold() - $buildType->getGold());
        $kingdom->setWood($kingdom->getWood() - $buildType->getWood());
        $kingdom->setStone($kingdom->getStone() - $buildType->getStone());
        $kingdom->setMetal($kingdom->getMetal() - $buildType->getMetal());

        return $kingdom;
    }

    public function getStack(string $resourceName)
    {
        $kingdom = $this->botManager->getKingdom();
        $goldHourly = $this->peopleManager->pay($kingdom);
        $foodHourly = $this->workManager->food($kingdom);
        $woodHourly = $this->workManager->wood($kingdom);
        $stoneHourly = $this->workManager->stone($kingdom);
        $metalHourly = $this->workManager->metal($kingdom);

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
            case ResourceInterface::RESOURCE_METAL:
                $stack = $metalHourly * $hours;
                break;
            default:
                break;
        }

        return $stack;
    }
}
