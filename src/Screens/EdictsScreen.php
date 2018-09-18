<?php

namespace Screens;

use Interfaces\ScreenInterface;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class EdictsScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $text = <<<TEXT
*{$this->title}*

В разработке
TEXT;

        $keyboard = new Keyboard(
            ['text'=> 'Постройки'],
            ['text' => 'Экономика'],
            ['text' => 'Люди'],
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
