<?php

namespace App\Manager;

use App\Entity\Kingdom;

class PeopleManager
{
    /**
     * 1 people eat food unit
     * If tax low, people eat lower
     * @param Kingdom $kingdom
     * @return float
     */
    public function eat(Kingdom $kingdom): float
    {
        return $kingdom->getPeople() * (1 / $kingdom->getTax());
    }

    /**
     * 1 people pay gold unit
     * If tax big, people pay more
     * @param Kingdom $kingdom
     * @return float
     */
    public function pay(Kingdom $kingdom): float
    {
        return $kingdom->getPeople() * (0.1 * $kingdom->getTax());
    }

    /**
     * @param Kingdom $kingdom
     * @return string
     */
    public function taxLevel(Kingdom $kingdom): string
    {
        $taxLevel = 'вручную';
        if ($kingdom->getTax() === 1) {
            $taxLevel = 'низкий';
        } else if ($kingdom->getTax() === 2) {
            $taxLevel = 'средний';
        } else if ($kingdom->getTax() === 3) {
            $taxLevel = 'высокий';
        }

        return $taxLevel;
    }
}
