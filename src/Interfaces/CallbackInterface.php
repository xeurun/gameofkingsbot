<?php

namespace App\Interfaces;

interface CallbackInterface
{
    /**
     * Пустышка
     * @var string
     */
    public const CALLBACK_MOCK = 'mock';
    /**
     * Every day bonus request
     * @var string
     */
    public const CALLBACK_EVERY_DAY_BONUS = 'every_day_bonus';
    /**
     * Raise or lower level of taxes
     * @var string
     */
    public const CALLBACK_RAISE_OR_LOWER_TAXES = 'raise_or_lower_taxes';
    /**
     * Get info
     * @var string
     */
    public const CALLBACK_GET_INFO = 'get_info';
    /**
     * Hire or fire people
     * @var string
     */
    public const CALLBACK_HIRE_OR_FIRE_PEOPLE = 'hire_or_fire_people';
    /**
     * Move resources to a warehouse
     * @var string
     */
    public const CALLBACK_MOVE_RESOURCES_TO_WAREHOUSE = 'move_resources_to_warehouse';
    /**
     * Increase the structure level
     * @var string
     */
    public const CALLBACK_INCREASE_STRUCTURE_LEVEL = 'increase_structure_level';
    /**
     * Change kingdom name request
     * @var string
     */
    public const CALLBACK_CHANGE_KINGDOM_NAME = 'change_kingdom_name';
}
