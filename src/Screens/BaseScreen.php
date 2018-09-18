<?php

namespace App\Screens;

use App\Manager\BotManager;

abstract class BaseScreen
{
    /** @var BotManager */
    protected $botManager;

    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
    }

    abstract public function execute(): void;
}
