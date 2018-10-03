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
    public const STRUCTURE_TYPE_CASTLE = 'structure_type_castle';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_TERRITORY = 'structure_type_territory';
    /**
     * Жилое здание.
     *
     * @var string
     */
    public const STRUCTURE_TYPE_LIFE_HOUSE = 'structure_type_lifehouse';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_BARN = 'structure_type_barn';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_SAWMILL = 'structure_type_sawmill';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_STONEMASON = 'structure_type_stonemason';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_SMELTERY = 'structure_type_smeltery';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_LIBRARY = 'structure_type_library';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_GARRISON = 'structure_type_garrison';
    /**
     * @var string
     */
    public const STRUCTURE_TYPE_MARKET = 'structure_type_market';
}
