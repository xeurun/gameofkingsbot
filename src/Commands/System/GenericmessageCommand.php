<?php

namespace App\Commands\System;

use App\Factory\ScreenFactory;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;


class GenericmessageCommand extends SystemCommand
{
    public function __construct(Telegram $telegram, Update $update = null)
    {
        $this->name = 'genericmessage';
        $this->description  = 'Handle generic message';
        $this->version = '1.0.0';
        $this->need_mysql = false;

        parent::__construct($telegram, $update);
    }

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        $callback = $this->getCallbackQuery();
        $message = $this->getMessage();
        $chatId = $message->getChat()->getId();

        $screen = null;
        $result = Request::emptyResponse();

        if (null === $callback) {
            $screenFactory = new ScreenFactory();
            $screenName = $message->getText();
            if ($screenFactory->isAvailableScreen($screenName)) {
                $screen = $screenFactory->createScreen($chatId, $screenName);
            }
        } else {
            $data  = $callback->getData();
            $chid = $callback->getFrom()->getId();
            // TODO: do it!
        }

        if (null !== $screen) {
            $result = $screen->execute();
        }

        return $result;
    }
}
