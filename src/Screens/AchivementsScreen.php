<?php

namespace App\Screens;

use App\Interfaces\CallbackInterface;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use App\Responses\BackResponse;

class AchivementsScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $text = <<<TEXT
*{$this->title}*

Заработать н золота: нет
Расширить крепость: нет
Получать н едениц древесины в день: да

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
