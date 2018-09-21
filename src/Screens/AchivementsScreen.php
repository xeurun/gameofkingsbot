<?php

namespace App\Screens;

use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
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
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_ACHIEVEMENTS;
        $text = <<<TEXT
*{$title}*

Мы уже учитываем выши заслуги, скоро их сможете увидеть и вы!
TEXT;

        $data = [
            'chat_id'      => $kingdom->getUser()->getId(),
            'text'         => $text,
            'parse_mode'   => 'Markdown',
        ];

        return Request::sendMessage($data);
    }
}
