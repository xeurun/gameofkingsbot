<?php

namespace App\Screens;

use App\Interfaces\ScreenInterface;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class MainMenuScreen extends BaseScreen
{
    /**
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $kingdom = $this->botManager->getKingdom();

        $keyboard = new Keyboard(
            [ScreenInterface::SCREEN_EDICTS, ScreenInterface::SCREEN_KINGDOM, ScreenInterface::SCREEN_TREASURE],
            [ScreenInterface::SCREEN_RESEARCH,  ScreenInterface::SCREEN_DIPLOMACY],
            [ScreenInterface::SCREEN_BONUSES, ScreenInterface::SCREEN_ACHIEVEMENTS, ScreenInterface::SCREEN_SETTINGS]
        );

        //Return a random keyboard.
        $keyboard = $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        $text = <<<TEXT
*🤴 {$kingdom->getName()} 👸*
TEXT;

        $data    = [
            'chat_id'      => $kingdom->getUser()->getId(),
            'text'         => $text,
            'reply_markup' => $keyboard,
            'parse_mode'   => 'Markdown'
        ];

        Request::sendMessage($data);

        $formatter = function ($value) {
            return $value;
        };

        $text = <<<TEXT
💰  Золота ({$formatter($kingdom->getGold())})
👪  Людей ({$formatter($kingdom->getPeople())})
🍞  Еды ({$formatter($kingdom->getFood())})
🌲  Древесины ({$formatter($kingdom->getWood())})
⛏  Камней ({$formatter($kingdom->getStone())})
🔨  Железа ({$formatter($kingdom->getMetal())})

Проверьте склад!
TEXT;

        $inlineKeyboard = new InlineKeyboard([
            ['text' => 'Новости', 'url' => 'https://t.me/worldofkings'],
            ['text' => 'Группа для общения', 'url' => 'https://t.me/placeofkings'],
        ]);

        $data = [
            'chat_id'      => $kingdom->getUser()->getId(),
            'text'         => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode'   => 'Markdown',
        ];

        return Request::sendMessage($data);
    }
}
