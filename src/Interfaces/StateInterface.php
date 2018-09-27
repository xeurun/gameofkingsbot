<?php

namespace App\Interfaces;

interface StateInterface
{
    /**
     * Wait input kingdom name
     * @var string
     */
    public const STATE_WAIT_KINGDOM_NAME = 'wait_kingdom_name';
    /**
     * Wait choose gender
     * @var string
     */
    public const STATE_WAIT_CHOOSE_GENDER = 'wait_choose_gender';
    /**
     * Wait choose lang
     * @var string
     */
    public const STATE_WAIT_CHOOSE_LANG = 'wait_choose_lang';
}
