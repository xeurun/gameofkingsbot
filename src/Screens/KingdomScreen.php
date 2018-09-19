<?php

namespace App\Screens;

use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class KingdomScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $text = <<<TEXT
*{$this->title}*

Король: ...........
В вашем королевстве ............... сейчас ..........

У вас 0 людей, 0 золота, 0 древесины, 0 еды

0 людей заняты добычей древесины
0 людей заняты добычей еды
0 людей заняты добычей золота
0 людей заняты поиском реликвий
0 людей заняты постройкой

Высокий уровень налогов

За последние н дней умерло н человек

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
