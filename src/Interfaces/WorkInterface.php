<?php

namespace App\Interfaces;

interface WorkInterface
{
    /** @var int */
    public const INITIAL_FOOD_SALARY = 30;
    /** @var int */
    public const INITIAL_WOOD_SALARY = 100;
    /** @var int */
    public const INITIAL_STONE_SALARY = 20;
    /** @var int */
    public const INITIAL_IRON_SALARY = 10;

    /** @var int */
    public const INITIAL_ON_ARMY = 0.1 * ResourceInterface::INITIAL_PEOPLE;
    /** @var int */
    public const INITIAL_ON_FOOD = 0.2 * ResourceInterface::INITIAL_PEOPLE;
    /** @var int */
    public const INITIAL_ON_WOOD = 0.4 * ResourceInterface::INITIAL_PEOPLE;
    /** @var int */
    public const INITIAL_ON_STONE = 0.15 * ResourceInterface::INITIAL_PEOPLE;
    /** @var int */
    public const INITIAL_ON_IRON = 0.15 * ResourceInterface::INITIAL_PEOPLE;

    /** @var int */
    public const INITIAL_MAX_ON_FOOD = self::INITIAL_ON_FOOD * 5;
    /** @var int */
    public const INITIAL_MAX_ON_WOOD = self::INITIAL_ON_WOOD * 5;
    /** @var int */
    public const INITIAL_MAX_ON_STONE = self::INITIAL_ON_STONE * 5;
    /** @var int */
    public const INITIAL_MAX_ON_IRON = self::INITIAL_ON_IRON * 5;

    /** @var string */
    public const WORK_TYPE_ARMY = 'work_type_army';
    /** @var string */
    public const WORK_TYPE_FOOD = 'work_type_food';
    /** @var string */
    public const WORK_TYPE_WOOD = 'work_type_wood';
    /** @var string */
    public const WORK_TYPE_STONE = 'work_type_stone';
    /** @var string */
    public const WORK_TYPE_IRON = 'work_type_iron';
}
