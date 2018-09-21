<?php

namespace App\Manager;


use App\Entity\Kingdom;

class WorkManager
{
    /**
     * 1 people made x food unit
     * If tax big, people made lower
     * @param Kingdom $kingdom
     * @return float
     */
    public function food(Kingdom $kingdom): float
    {
        return $kingdom->getOnFood() * 2 / $kingdom->getTax();
    }

    /**
     * 1 people made x wood unit
     * If tax big, people made lower
     * @param Kingdom $kingdom
     * @return float
     */
    public function wood(Kingdom $kingdom): float
    {
        return $kingdom->getOnWood() * 1 / $kingdom->getTax();
    }

    /**
     * 1 people made x stone unit
     * @param Kingdom $kingdom
     * @return float
     */
    public function stone(Kingdom $kingdom): float
    {
        return 0;
    }

    /**
     * 1 people made x metal unit
     * @param Kingdom $kingdom
     * @return float
     */
    public function metal(Kingdom $kingdom): float
    {
        return 0;
    }

    /**
     * @param Kingdom $kingdom
     * @return int
     */
    public function free(Kingdom $kingdom): int
    {
        return $kingdom->getPeople() - $kingdom->getOnFood() - $kingdom->getOnWood()
            - $kingdom->getOnStone() - $kingdom->getOnMetal() - $kingdom->getOnBuildings();
    }
}
