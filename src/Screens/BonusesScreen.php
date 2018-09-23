<?php

namespace App\Screens;

use App\Interfaces\CallbackInterface;
use App\Interfaces\ScreenInterface;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use App\Responses\BackResponse;

class BonusesScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_BONUSES;
        $text = <<<TEXT
*{$title}*

Вступайте в нашу группу, подписывайтесь на наш канал и получайте дополнительные ежедневные бонусы!

TEXT;
        $inlineKeyboard = new InlineKeyboard(
            [
                ['text' => 'Вступить в группу', 'url' => 'https://t.me/worldofkings'],
                ['text' => 'Подписаться на канал', 'url' => 'https://t.me/placeofkings'],
            ], [
                ['text' => 'Получить ежедневный бонус', 'callback_data' => CallbackInterface::CALLBACK_EVERY_DAY_BONUS],
            ]
        );

        $data = [
            'chat_id'      => $kingdom->getUser()->getId(),
            'text'         => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode'   => 'Markdown',
        ];

        return Request::sendMessage($data);
    }
}
