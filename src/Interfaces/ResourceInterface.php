<?php

namespace App\Interfaces;

interface ResourceInterface
{
    /** @var int  */
    public const INITIAL_EAT_FOOD = 5;

    /** @var int  */
    public const EVERY_DAY_FOOD_BONUS = 500;
    /** @var int  */
    public const EVERY_DAY_GOLD_BONUS = 500;
    /** @var int  */
    public const EVERY_DAY_WOOD_BONUS = 4000;
    /** @var int  */
    public const EVERY_DAY_STONE_BONUS = 75;
    /** @var int  */
    public const EVERY_DAY_IRON_BONUS = 45;

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

    /** @var string */
    public const RESOURCE_GOLD = 'gold';
    /** @var string */
    public const RESOURCE_FOOD = 'food';
    /** @var string */
    public const RESOURCE_WOOD = 'wood';
    /** @var string */
    public const RESOURCE_STONE = 'stone';
    /** @var string */
    public const RESOURCE_IRON = 'iron';
}
