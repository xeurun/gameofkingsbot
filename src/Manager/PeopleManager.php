<?php

namespace App\Manager;

use App\Entity\Kingdom;
use App\Interfaces\ResourceInterface;
use App\Interfaces\TaxesInterface;

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
        return round($kingdom->getPeople() * (ResourceInterface::INITIAL_EAT_FOOD / $kingdom->getTax()));
    }

    /**
     * 1 people pay gold unit
     * If tax big, people pay more
     * @param Kingdom $kingdom
     * @return float
     */
    public function pay(Kingdom $kingdom): float
    {
        return round($kingdom->getPeople() * (TaxesInterface::INITIAL_TAXES_SIZE * $kingdom->getTax()));
    }
}
