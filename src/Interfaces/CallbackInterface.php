<?php

namespace App\Interfaces;

interface CallbackInterface
{
    /**
     * Пустышка.
     *
     * @var string
     */
    public const CALLBACK_MOCK = 'callback_mock';
    /**
     * Every day bonus request.
     *
     * @var string
     */
    public const CALLBACK_EVERY_DAY_BONUS = 'callback_every_day_bonus';
    /**
     * Raise or lower level of taxes.
     *
     * @var string
     */
    public const CALLBACK_RAISE_OR_LOWER_TAXES = 'callback_raise_or_lower_taxes';
    /**
     * Get info.
     *
     * @var string
     */
    public const CALLBACK_GET_INFO = 'callback_get_info';
    /**
     * Hire or fire people.
     *
     * @var string
     */
    public const CALLBACK_HIRE_OR_FIRE_PEOPLE = 'callback_hire_or_fire_people';
    /**
     * Move resources to a warehouse.
     *
     * @var string
     */
    public const CALLBACK_MOVE_RESOURCES_TO_WAREHOUSE = 'callback_move_resources_to_warehouse';
    /**
     * Increase the structure level.
     *
     * @var string
     */
    public const CALLBACK_INCREASE_STRUCTURE_LEVEL = 'callback_increase_structure_level';
    /**
     * Change state.
     *
     * @var string
     */
    public const CALLBACK_CHANGE_STATE = 'callback_change_state';
    /**
     * Adviser.
     *
     * @var string
     */
    public const CALLBACK_ADVISER = 'callback_adviser';
}
