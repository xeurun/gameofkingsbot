<?php

namespace App\States;

use App\Manager\BotManager;
use Longman\TelegramBot\Entities\Message;

abstract class BaseState
{
    /** @var BotManager */
    protected $botManager;

    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
    }

    abstract public function preExecute(): void;

    abstract public function execute(Message $message): void;
}
