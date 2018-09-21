<?php

namespace App\Screens;

use App\Entity\Kingdom;
use App\Manager\BotManager;
use Longman\TelegramBot\Entities\ServerResponse;

abstract class BaseScreen
{
    protected $botManager;

    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
    }

    abstract public function execute(): ServerResponse;
}
