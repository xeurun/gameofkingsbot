<?php

namespace App\Screens;

use App\Manager\BotManager;

abstract class BaseScreen
{
    /** @var BotManager  */
    protected $botManager;

    /**
     * @param BotManager $botManager
     */
    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
    }

    /**
     * @return void
     */
    abstract public function execute(): void;
}
