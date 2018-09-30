<?php

namespace App\Interfaces;

interface AdviserInterface
{
    /** @var int */
    public const ADVISER_SHOW_INITIAL_TUTORIAL = 'adviser_show_initial_tutorial';
    /** @var int */
    public const ADVISER_SHOW_WAREHOUSE_TUTORIAL = 'adviser_show_warehouse_tutorial';
    /** @var int */
    public const ADVISER_SHOW_EDICTS_TUTORIAL = 'adviser_show_edicts_tutorial';
    /** @var int */
    public const ADVISER_SHOW_BUILDINGS_TUTORIAL = 'adviser_show_buildings_tutorial';
    /** @var int */
    public const ADVISER_SHOW_PEOPLE_TUTORIAL = 'adviser_show_people_tutorial';
    /** @var int */
    public const ADVISER_SHOW_BONUSES_TUTORIAL = 'adviser_show_bonuses_tutorial';
}
