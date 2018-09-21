<?php

namespace App\Manager;

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

    public function getStack(string $resouceName)
    {
        $kingdom = $this->botManager->getKingdom();
        $goldHourly = $this->peopleManager->pay($kingdom);
        $foodHourly = $this->workManager->food($kingdom);
        $woodHourly = $this->workManager->wood($kingdom);
        $stoneHourly = $this->workManager->stone($kingdom);
        $metalHourly = $this->workManager->metal($kingdom);

        $now = new \DateTime();
        $diff = $now->diff($kingdom->getGrabResourcesDate());
        $hours = $diff->h + (($diff->d * ($diff->m * $diff->y)) * 24);

        switch ($resouceName) {
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
