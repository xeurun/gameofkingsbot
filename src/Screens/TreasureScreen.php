<?php

namespace Screens;

use Interfaces\CallbackInterface;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class TreasureScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $text = <<<TEXT
*{$this->title}*

У вас 0 золота, 0 древесины, 0 еды

0 золота добывается в час
0 еды добывается в час
0 золота добывается в час

В разработке
TEXT;
        $inlineKeyboard = new InlineKeyboard(
            [
                ['text' => 'В разработке', 'callback_data' => CallbackInterface::CALLBACK_MOCK],
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
