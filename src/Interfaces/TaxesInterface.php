<?php

namespace App\Interfaces;

interface TaxesInterface
{
    /** @var int */
    public const INITIAL_TAXES_SIZE = 5;
    /** @var int */
    public const INITIAL_TAXES_LEVEL = self::TAXES_LEVEL_MEDIUM;
    /** @var string */
    public const TAXES_LEVEL_LOW = 1;
    /** @var string */
    public const TAXES_LEVEL_MEDIUM = 2;
    /** @var string */
    public const TAXES_LEVEL_HIGH = 3;
    /** @var string */
    public const TAXES_LOW = 'taxes_low';
    /** @var string */
    public const TAXES_MEDIUM = 'taxes_medium';
    /** @var string */
    public const TAXES_HIGH = 'taxes_high';
    /** @var string */
    public const TAXES_CUSTOM = 'taxes_custom';
    /** @var string */
    public const TAXES = 'taxes';
}
