<?php

namespace App\Screens;

use App\Interfaces\CallbackInterface;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use App\Responses\BackResponse;

class SettingsScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $text = <<<TEXT
*{$this->title}*

Ваше имя: ...........
Название вашего королевства: ...............

Всего королей:

Администрация:

В разработке
TEXT;

        $inlineKeyboard = new InlineKeyboard(
            [
                ['text' => 'Изменить имя', 'callback_data' => CallbackInterface::CALLBACK_INPUT_NEW_KING_NAME],
            ],
            [
                ['text' => 'Изменить название королества', 'callback_data' => CallbackInterface::CALLBACK_INPUT_NEW_KINGDOM_NAME],
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
