<?php

namespace App\Callbacks;

use App\Manager\BotManager;
use Longman\TelegramBot\Entities\ServerResponse;

abstract class BaseCallback
{
    /** @var BotManager */
    protected $botManager;

    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
    }

    abstract public function execute(): ServerResponse;
}
