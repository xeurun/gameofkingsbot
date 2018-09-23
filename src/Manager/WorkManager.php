<?php

namespace App\Manager;


use App\Entity\Kingdom;
use App\Interfaces\WorkInterface;

class WorkManager
{
    /**
     * @param Kingdom $kingdom
     * @return int
     */
    public function workedHours(Kingdom $kingdom): int
    {
        $now = new \DateTime();
        $diff = $now->diff($kingdom->getGrabResourcesDate());

        return $diff->h + ($diff->days * 24);
    }

    /**
     * 1 people made x food unit
     * If tax big, people made lower
     * @param Kingdom $kingdom
     * @return float
     */
    public function food(Kingdom $kingdom): float
    {
        return round($kingdom->getOnFood() * WorkInterface::INITIAL_FOOD_SALARY / $kingdom->getTax());
    }

    /**
     * 1 people made x wood unit
     * If tax big, people made lower
     * @param Kingdom $kingdom
     * @return float
     */
    public function wood(Kingdom $kingdom): float
    {
        return round($kingdom->getOnWood() * WorkInterface::INITIAL_WOOD_SALARY / $kingdom->getTax());
    }

    /**
     * 1 people made x stone unit
     * @param Kingdom $kingdom
     * @return float
     */
    public function stone(Kingdom $kingdom): float
    {
        return round($kingdom->getOnStone() * WorkInterface::INITIAL_STONE_SALARY / $kingdom->getTax());
    }

    /**
     * 1 people made x iron unit
     * @param Kingdom $kingdom
     * @return float
     */
    public function iron(Kingdom $kingdom): float
    {
        return round($kingdom->getOnIron() * WorkInterface::INITIAL_IRON_SALARY / $kingdom->getTax());
    }

    /**
     * @param Kingdom $kingdom
     * @return int
     */
    public function free(Kingdom $kingdom): int
    {
        return $kingdom->getPeople() - $kingdom->getOnFood() - $kingdom->getOnWood()
            - $kingdom->getOnStone() - $kingdom->getOnIron() - $kingdom->getOnStructure();
    }

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
            case WorkInterface::WORK_TYPE_STRUCTURE:
                $workerCount = $kingdom->getOnStructure();
                break;
            default:
                throw new \InvalidArgumentException('Invalid work type');
                break;
        }

        return $workerCount;
    }

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
            case WorkInterface::WORK_TYPE_STRUCTURE:
                $kingdom->setOnStructure($kingdom->getOnStructure() + 1);
                break;
            default:
                throw new \InvalidArgumentException('Invalid work type');
                break;
        }
    }

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
            case WorkInterface::WORK_TYPE_STRUCTURE:
                $kingdom->setOnStructure($kingdom->getOnStructure() - 1);
                break;
            default:
                throw new \InvalidArgumentException('Invalid work type');
                break;
        }
    }
}
