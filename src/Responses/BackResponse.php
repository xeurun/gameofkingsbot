<?php

namespace Responses;

use Interfaces\ScreenInterface;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class BackResponse
{
    private $chatId;
    private $text;

    public function __construct($chatId, $text = '')
    {
        $this->chatId = $chatId;
        $this->text = $text;
    }

    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $keyboard = new Keyboard([
            ['text' => ScreenInterface::SCREEN_BACK]
        ]);

        //Return a random keyboard.
        $keyboard = $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(false);

        $data    = [
            'chat_id'      => $this->chatId,
            'text'         => $this->text,
            'reply_markup' => $keyboard,
            'parse_mode'   => 'Markdown'
        ];

        return Request::sendMessage($data);
    }
}
