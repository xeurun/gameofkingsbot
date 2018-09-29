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
     * 1 people made x food unit
     * If tax big, people made lower
     * @return float
     */
    public function food(): float
    {
        $kingdom = $this->botManager->getKingdom();
        return round($kingdom->getOnFood() * WorkInterface::INITIAL_FOOD_SALARY / $kingdom->getTax());
    }

    /**
     * 1 people made x wood unit
     * If tax big, people made lower
     * @return float
     */
    public function wood(): float
    {
        $kingdom = $this->botManager->getKingdom();
        return round($kingdom->getOnWood() * WorkInterface::INITIAL_WOOD_SALARY / $kingdom->getTax());
    }

    /**
     * 1 people made x stone unit
     * @return float
     */
    public function stone(): float
    {
        $kingdom = $this->botManager->getKingdom();
        return round($kingdom->getOnStone() * WorkInterface::INITIAL_STONE_SALARY / $kingdom->getTax());
    }

    /**
     * 1 people made x iron unit
     * @return float
     */
    public function iron(): float
    {
        $kingdom = $this->botManager->getKingdom();
        return round($kingdom->getOnIron() * WorkInterface::INITIAL_IRON_SALARY / $kingdom->getTax());
    }

    /**
     * @return int
     */
    public function free(): int
    {
        $kingdom = $this->botManager->getKingdom();
        $people = $this->kingdomManager->getPeople();
        return $people - $kingdom->getOnFood() - $kingdom->getOnWood()
            - $kingdom->getOnStone() - $kingdom->getOnIron() - $kingdom->getOnArmy();
    }

    /**
     * @param string $workType
     * @return bool
     */
    public function checkLimit(string $workType): bool
    {
        $kingdom = $this->botManager->getKingdom();
        $max = $this->kingdomManager->getMaxOn($workType);
        return $this->workerCount($kingdom, $workType) < $max;
    }

    /**
     * @param Kingdom $kingdom
     * @param string $workType
     * @return int
     */
    public function workerCount(Kingdom $kingdom, string $workType): int
    {
        switch ($workType) {
            case WorkInterface::WORK_TYPE_FOOD:
                $workerCount = $kingdom->getOnFood();
                break;
            case WorkInterface::WORK_TYPE_WOOD:
                $workerCount = $kingdom->getOnWood();
                break;
            case WorkInterface::WORK_TYPE_STONE:
                $workerCount = $kingdom->getOnStone();
                break;
            case WorkInterface::WORK_TYPE_IRON:
                $workerCount = $kingdom->getOnIron();
                break;
            case WorkInterface::WORK_TYPE_ARMY:
                $workerCount = $kingdom->getOnArmy();
                break;
            default:
                throw new \InvalidArgumentException('Invalid work type');
                break;
        }

        return $workerCount;
    }

    /**
     * @param Kingdom $kingdom
     * @param string $workType
     */
    public function hire(Kingdom $kingdom, string $workType): void
    {
        switch ($workType) {
            case WorkInterface::WORK_TYPE_FOOD:
                $kingdom->setOnFood($kingdom->getOnFood() + 1);
                break;
            case WorkInterface::WORK_TYPE_WOOD:
                $kingdom->setOnWood($kingdom->getOnWood() + 1);
                break;
            case WorkInterface::WORK_TYPE_STONE:
                $kingdom->setOnStone($kingdom->getOnStone() + 1);
                break;
            case WorkInterface::WORK_TYPE_IRON:
                $kingdom->setOnIron($kingdom->getOnIron() + 1);
                break;
            case WorkInterface::WORK_TYPE_ARMY:
                $kingdom->setOnArmy($kingdom->getOnArmy() + 1);
                break;
            default:
                throw new \InvalidArgumentException('Invalid work type');
                break;
        }
    }

    /**
     * @param Kingdom $kingdom
     * @param string $workType
     */
    public function fire(Kingdom $kingdom, string $workType): void
    {
        switch ($workType) {
            case WorkInterface::WORK_TYPE_FOOD:
                $kingdom->setOnFood($kingdom->getOnFood() - 1);
                break;
            case WorkInterface::WORK_TYPE_WOOD:
                $kingdom->setOnWood($kingdom->getOnWood() - 1);
                break;
            case WorkInterface::WORK_TYPE_STONE:
                $kingdom->setOnStone($kingdom->getOnStone() - 1);
                break;
            case WorkInterface::WORK_TYPE_IRON:
                $kingdom->setOnIron($kingdom->getOnIron() - 1);
                break;
            case WorkInterface::WORK_TYPE_ARMY:
                $kingdom->setOnArmy($kingdom->getOnArmy() - 1);
                break;
            default:
                throw new \InvalidArgumentException('Invalid work type');
                break;
        }
    }
}
