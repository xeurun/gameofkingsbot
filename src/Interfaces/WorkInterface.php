<?php

namespace App\Interfaces;

interface WorkInterface
{
    /** @var string */
    public const WORK_TYPE_FOOD = 'food';
    /** @var string */
    public const WORK_TYPE_WOOD = 'wood';
    /** @var string */
    public const WORK_TYPE_STONE = 'stone';
    /** @var string */
    public const WORK_TYPE_IRON = 'iron';
    /** @var string */
    public const WORK_TYPE_STRUCTURE = 'structure';
    /** @var int */
    public const INITIAL_FOOD_SALARY = 30;
    /** @var int */
    public const INITIAL_WOOD_SALARY = 100;
    /** @var int */
    public const INITIAL_STONE_SALARY = 5;
    /** @var int */
    public const INITIAL_IRON_SALARY = 3;
    /** @var int */
    public const INITIAL_FOOD_WORKER = 20;
    /** @var int */
    public const INITIAL_WOOD_WORKER = 40;
    /** @var int */
    public const INITIAL_STONE_WORKER = 15;
    /** @var int */
    public const INITIAL_IRON_WORKER = 15;
    /** @var int */
    public const INITIAL_STRUCTURE_WORKER = 10;
}
