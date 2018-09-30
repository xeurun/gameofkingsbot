<?php

namespace App\Interfaces;

interface ResourceInterface
{
    /** @var int  */
    public const INITIAL_EAT_FOOD = 5;

    /** @var int  */
    public const INITIAL_PEOPLE = 100;
    /** @var int  */
    public const INITIAL_FOOD = 12000;
    /** @var int  */
    public const INITIAL_GOLD = 6000;
    /** @var int  */
    public const INITIAL_WOOD = 120000;
    /** @var int  */
    public const INITIAL_STONE = 6000;
    /** @var int  */
    public const INITIAL_IRON = 3600;

    /** @var int  */
    public const INITIAL_PEOPLE_MAX = self::INITIAL_PEOPLE * 5;
    /** @var int  */
    public const INITIAL_FOOD_MAX = self::INITIAL_FOOD * 5;
    /** @var int  */
    public const INITIAL_GOLD_MAX = self::INITIAL_GOLD * 5;
    /** @var int  */
    public const INITIAL_WOOD_MAX = self::INITIAL_WOOD * 5;
    /** @var int  */
    public const INITIAL_STONE_MAX = self::INITIAL_STONE * 5;
    /** @var int  */
    public const INITIAL_IRON_MAX = self::INITIAL_IRON * 5;

    /** @var int  */
    public const EVERY_DAY_FOOD_BONUS = 5000;
    /** @var int  */
    public const EVERY_DAY_GOLD_BONUS = 5000;
    /** @var int  */
    public const EVERY_DAY_WOOD_BONUS = 4000;
    /** @var int  */
    public const EVERY_DAY_STONE_BONUS = 7500;
    /** @var int  */
    public const EVERY_DAY_IRON_BONUS = 4500;

    /** @var string */
    public const RESOURCE_PEOPLE = 'resource_people';
    /** @var string */
    public const RESOURCE_GOLD = 'resource_gold';
    /** @var string */
    public const RESOURCE_FOOD = 'resource_food';
    /** @var string */
    public const RESOURCE_WOOD = 'resource_wood';
    /** @var string */
    public const RESOURCE_STONE = 'resource_stone';
    /** @var string */
    public const RESOURCE_IRON = 'resource_iron';
}
