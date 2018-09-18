<?php

namespace App\Callbacks;

use App\Manager\BotManager;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;

abstract class BaseCallback
{
    /** @var CallbackQuery */
    protected $callbackQuery;
    /** @var BotManager */
    protected $botManager;
    /** @var Message */
    protected $message;

    /**
     * BaseCallback constructor.
     */
    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
        $this->callbackQuery = $this->botManager->getCallbackQuery();
        if (!$this->callbackQuery) {
            throw new \InvalidArgumentException('Callback query null');
        }

        $message = $this->callbackQuery->getMessage();
        if (null === $message) {
            throw new \LogicException('Callback not work without message');
        }

        $this->message = $message;
    }

    /**
     * Execute callback end return end response.
     */
    abstract public function execute(): ServerResponse;
}
