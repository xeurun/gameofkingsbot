<?php

namespace App\Screens;

use App\Manager\BotManager;
use Longman\TelegramBot\Entities\ServerResponse;

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

    abstract public function execute(): ServerResponse;
}
