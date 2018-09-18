<?php

namespace Screens;

use Interfaces\CallbackInterface;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Responses\BackResponse;

class BonusesScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $text = <<<TEXT
*{$this->title}*

Вступить в группу: нет
Подписаться на канал: нет
Вступить в группу н: да

В разработке
TEXT;
        $inlineKeyboard = new InlineKeyboard(
            [
                ['text' => 'Вступить в группу', 'callback_data' => CallbackInterface::CALLBACK_MOCK],
                ['text' => 'Подписаться на канал', 'callback_data' => CallbackInterface::CALLBACK_MOCK],
            ]
        );

        $data = [
            'chat_id'      => $this->chatId,
            'text'         => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode'   => 'Markdown',
        ];

        return Request::sendMessage($data);
    }
}
