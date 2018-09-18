<?php

namespace Screens;

use Interfaces\ScreenInterface;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Responses\BackResponse;

class DiplomacyScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $text = <<<TEXT
*{$this->title}*

Вы верховный король
или
Ваш верховный король ....

В разработке
TEXT;

        $keyboard = new Keyboard(
            ['text'=> 'Ваши друзья'],
            ['text' => 'Ваши враги'],
            ['text' => 'Ваши вассалы'],
            ['text' => ScreenInterface::SCREEN_BACK]
        );

        //Return a random keyboard.
        $keyboard = $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        $data    = [
            'chat_id'      => $this->chatId,
            'text'         => $text,
            'reply_markup' => $keyboard,
            'parse_mode'   => 'Markdown'
        ];

        return Request::sendMessage($data);
    }
}
