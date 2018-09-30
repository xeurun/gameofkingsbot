<?php

namespace App\Manager;

use App\Entity\Kingdom;
use App\Interfaces\WorkInterface;

class WorkManager
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
     * @return int
     */
    public function workedHours(): int
    {
        $kingdom = $this->botManager->getKingdom();

        $now = new \DateTime();
        $diff = $now->diff($kingdom->getGrabResourcesDate());

        return $diff->h + ($diff->days * 24);
    }

    /**
     * @param string $workType
     * @return float
     */
    public function getSalary(string $workType): float
    {
        $kingdom = $this->botManager->getKingdom();
        switch ($workType) {
            case WorkInterface::WORK_TYPE_FOOD:
                $value = round(
                    $kingdom->getWorkerCount($workType)
                    * WorkInterface::INITIAL_FOOD_SALARY
                    / $kingdom->getTax()
                );
                break;
            case WorkInterface::WORK_TYPE_WOOD:
                $value = round(
                    $kingdom->getWorkerCount($workType)
                    * WorkInterface::INITIAL_WOOD_SALARY
                    / $kingdom->getTax()
                );
                break;
            case WorkInterface::WORK_TYPE_STONE:
                $value = round(
                    $kingdom->getWorkerCount($workType)
                    * WorkInterface::INITIAL_STONE_SALARY
                    / $kingdom->getTax()
                );
                break;
            case WorkInterface::WORK_TYPE_IRON:
                $value = round(
                    $kingdom->getWorkerCount($workType)
                    * WorkInterface::INITIAL_IRON_SALARY
                    / $kingdom->getTax()
                );
                break;
            default:
                throw new \InvalidArgumentException('Undefined resource type!');
        }

        return $value;
    }

    /**
     * @return int
     */
    public function free(): int
    {
        $kingdom = $this->botManager->getKingdom();
        $people = $this->kingdomManager->getPeople();
        return $people
            - $kingdom->getWorkerCount(WorkInterface::WORK_TYPE_FOOD)
            - $kingdom->getWorkerCount(WorkInterface::WORK_TYPE_WOOD)
            - $kingdom->getWorkerCount(WorkInterface::WORK_TYPE_STONE)
            - $kingdom->getWorkerCount(WorkInterface::WORK_TYPE_IRON)
            - $kingdom->getWorkerCount(WorkInterface::WORK_TYPE_ARMY);
    }

    /**
     * @param string $workType
     * @return bool
     */
    public function checkLimit(string $workType): bool
    {
        $kingdom = $this->botManager->getKingdom();
        $max = $this->kingdomManager->getMaxOn($workType);

        return $kingdom->getWorkerCount($workType) < $max;
    }
}
