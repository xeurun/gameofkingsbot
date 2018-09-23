<?php

namespace App\Interfaces;

interface StructureInterface
{
    /**
     * @var int
     */
    public const INITIAL_STRUCTURE_LEVEL = 1;
    /**
     * @var int
     */
    public const STRUCTURE_TYPE_LIFE_HOUSE_ADD_PEOPLE = 10;
    /**
     * @var int
     */
    public const STRUCTURE_TYPE_TERRITORY_ADD_SIZE = 5;
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_CASTLE = 'castle';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_TERRITORY = 'territory';
    /**
     * Жилое здание
     * @var string
     */
    public const STRUCTURE_TYPE_LIFE_HOUSE = 'lifehouse';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_BARN = 'barn';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_SAWMILL = 'sawmill';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_STONEMASON = 'stonemason';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_SMELTERY = 'smeltery';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_LIBRARY = 'library';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_GARRISON = 'garrison';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_MARKET = 'market';
}
