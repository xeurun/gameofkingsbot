<?php

namespace App\States;

use App\Manager\BotManager;

abstract class BaseState
{
    /** @var BotManager */
    protected $botManager;

    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
    }

    abstract public function execute(): void;
}
