<?php

namespace App\Screens;

use App\Interfaces\ScreenInterface;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use App\Responses\BackResponse;

class DiplomacyScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_DIPLOMACY;
        $text = <<<TEXT
*{$title}*

В данный момент, в этом мире, королевства только начинают развиваться, но скоро начнется эпоха войн и дипломатии

TEXT;

        $keyboard = new Keyboard(
            //['text'=> 'Ваши друзья'],
            // ['text' => 'Ваши враги'],
            ['text' => ScreenInterface::SCREEN_BACK]
        );

        //Return a random keyboard.
        $keyboard = $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        $data    = [
            'chat_id'      => $kingdom->getUser()->getId(),
            'text'         => $text,
            'reply_markup' => $keyboard,
            'parse_mode'   => 'Markdown'
        ];

        return Request::sendMessage($data);
    }
}
