<?php

namespace App\Interfaces;

interface StateInterface
{
    /**
     * Wait input kingdom name
     * @var string
     */
    public const STATE_WAIT_INPUT_KINGDOM_NAME = 'state_wait_kingdom_name';
    /**
     * Wait choose gender
     * @var string
     */
    public const STATE_WAIT_CHOOSE_GENDER = 'state_wait_choose_gender';
    /**
     * Wait choose lang
     * @var string
     */
    public const STATE_WAIT_CHOOSE_LANG = 'state_wait_choose_lang';
    /**
     * Wait input name
     * @var string
     */
    public const STATE_WAIT_INPUT_NAME = 'state_wait_input_name';
}
