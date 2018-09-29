<?php

namespace App\States;

use App\Manager\BotManager;
use Longman\TelegramBot\Entities\Message;

abstract class BaseState
{
    /** @var BotManager */
    protected $botManager;
    /** @var Message */
    protected $message;

    /**
     * @param BotManager $botManager
     */
    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
        if (null === $this->botManager->getMessage()) {
            throw new \LogicException('State not work without message');
        }
        $this->message = $this->botManager->getMessage();
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    abstract public function preExecute(): void;
    abstract public function execute(): void;
}
