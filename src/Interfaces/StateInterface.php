<?php

namespace App\Interfaces;

interface StateInterface
{
    /**
     * New player
     * @var string
     */
    public const STATE_NEW_PLAYER = 'new_player';
    /**
     * Wait input kingdom name
     * @var string
     */
    public const STATE_WAIT_KINGDOM_NAME = 'wait_kingdom_name';
}
