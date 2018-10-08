<?php

namespace App\Manager;

use App\Helper\DateTimeHelper;
use App\Interfaces\WorkInterface;

class WorkManager
{
    /** @var BotManager */
    protected $botManager;
    /** @var KingdomManager */
    protected $kingdomManager;

    public function __construct(BotManager $botManager, KingdomManager $kingdomManager)
    {
        $this->botManager = $botManager;
        $this->kingdomManager = $kingdomManager;
    }

    public function workedHours(): int
    {
        $kingdom = $this->botManager->getKingdom();
        return DateTimeHelper::hourBetween(null, $kingdom->getGrabResourcesDate());
    }

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

    public function hasFreeSpaceFor(string $workType, int $count = 1): bool
    {
        $kingdom = $this->botManager->getKingdom();
        $max = $this->kingdomManager->getMaxOn($workType);

        return $kingdom->getWorkerCount($workType) + $count <= $max;
    }
}
