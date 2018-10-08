<?php

namespace App\Interfaces;

interface ResourceInterface
{
    /** @var int */
    public const INITIAL_EAT_FOOD = 10;

    /** @var int */
    public const MIN_ALIVE_PEOPLE = 50;
    /** @var int */
    public const INITIAL_PEOPLE = 100;
    /** @var int */
    public const INITIAL_FOOD = 1200;
    /** @var int */
    public const INITIAL_GOLD = 600;
    /** @var int */
    public const INITIAL_WOOD = 1750;
    /** @var int */
    public const INITIAL_STONE = 700;
    /** @var int */
    public const INITIAL_IRON = 525;

    /** @var int */
    public const INITIAL_PEOPLE_MAX = self::INITIAL_PEOPLE * 3;
    /** @var int */
    public const INITIAL_FOOD_MAX = self::INITIAL_FOOD * 3;
    /** @var int */
    public const INITIAL_GOLD_MAX = self::INITIAL_GOLD * 3;
    /** @var int */
    public const INITIAL_WOOD_MAX = self::INITIAL_WOOD * 3;
    /** @var int */
    public const INITIAL_STONE_MAX = self::INITIAL_STONE * 3;
    /** @var int */
    public const INITIAL_IRON_MAX = self::INITIAL_IRON * 3;

    /** @var int */
    public const EVERY_DAY_FOOD_BONUS = self::INITIAL_FOOD;
    /** @var int */
    public const EVERY_DAY_GOLD_BONUS = self::INITIAL_GOLD;
    /** @var int */
    public const EVERY_DAY_WOOD_BONUS = self::INITIAL_WOOD;
    /** @var int */
    public const EVERY_DAY_STONE_BONUS = self::INITIAL_STONE;
    /** @var int */
    public const EVERY_DAY_IRON_BONUS = self::INITIAL_IRON;

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
