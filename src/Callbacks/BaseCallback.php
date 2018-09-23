<?php

namespace App\Callbacks;

use App\Manager\BotManager;
use Longman\TelegramBot\Entities\CallbackQuery;
use Longman\TelegramBot\Entities\ServerResponse;

abstract class BaseCallback
{
    /** @var CallbackQuery */
    protected $callbackQuery;
    /** @var BotManager */
    protected $botManager;

    public function __construct(BotManager $botManager)
    {
        $this->botManager = $botManager;
        $this->callbackQuery = $this->botManager->getCallbackQuery();
        if (!$this->callbackQuery) {
            throw new \InvalidArgumentException('Callback query null');
        }
    }

    /**
     * Execute callback end return end response
     * @return ServerResponse
     */
    abstract public function execute(): ServerResponse;
}
