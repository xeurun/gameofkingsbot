<?php

namespace App\States;

use App\Manager\BotManager;
use Longman\TelegramBot\Entities\Message;

abstract class BaseState
{
    /** @var BotManager */
    protected $botManager;

    /**
     * BaseState constructor.
     */
    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
    }

    /**
     * Pre execute.
     */
    abstract public function preExecute(): void;

    /**
     * Execute.
     */
    abstract public function execute(Message $message): void;
}
