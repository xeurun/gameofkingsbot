<?php

namespace App\Manager;

use App\Entity\Kingdom;
use App\Interfaces\BuildInterface;

class KingdomManager
{
    /**
     * @param Kingdom $kingdom
     * @return float
     */
    public function level(Kingdom $kingdom): float
    {
        $castle = $kingdom->getBuild(BuildInterface::BUILD_TYPE_CASTLE);
        if ($castle) {
            $level = $castle->getLevel();
        } else {
            $level = 0;
        }

        return $level;
    }
}
