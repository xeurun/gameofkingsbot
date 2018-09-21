<?php

namespace App\Interfaces;

interface CallbackInterface
{
    /** @var string  */
    public const CALLBACK_MOCK = 'mock';
    /** @var string  */
    public const CALLBACK_EVERY_DAY_BONUS = 'every_day_bonus';
    /** @var string  */
    public const CALLBACK_UP_DOWN_TAX = 'up_down_tax';
    /** @var string  */
    public const CALLBACK_UP_DOWN_WORKER = 'up_down_worker';
    /** @var string  */
    public const CALLBACK_GRAB_RESOURCES = 'grab_resouces';
    /** @var string  */
    public const CALLBACK_CHANGE_KINGDOM_NAME = 'change_kingdom_name';
}
