<?php

namespace Screens;

use Interfaces\ScreenInterface;
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
        $keyboard = new Keyboard(
            [ScreenInterface::SCREEN_KINGDOM, ScreenInterface::SCREEN_EDICTS, ScreenInterface::SCREEN_TREASURE],
            [ScreenInterface::SCREEN_TODO1,  ScreenInterface::SCREEN_DIPLOMACY, ScreenInterface::SCREEN_TODO2],
            [ScreenInterface::SCREEN_BONUSES, ScreenInterface::SCREEN_ACHIEVEMENTS, ScreenInterface::SCREEN_SETTINGS]
        );

        //Return a random keyboard.
        $keyboard = $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        $text = <<<TEXT
*{$this->title}*
TEXT;

        $data    = [
            'chat_id'      => $this->chatId,
            'text'         => $text,
            'reply_markup' => $keyboard,
            'parse_mode'   => 'Markdown'
        ];

        Request::sendMessage($data);

        $text = <<<TEXT
У вас:

    Людей 👪 (0)
TEXT;

        $inlineKeyboard = new InlineKeyboard([
            ['text' => 'Новости', 'url' => 'https://t.me/worldofkings'],
            ['text' => 'Группа для общения', 'url' => 'https://t.me/placeofkings'],
        ]);

        $data = [
            'chat_id'      => $this->chatId,
            'text'         => $text,
            'reply_markup' => $inlineKeyboard,
            'parse_mode'   => 'Markdown',
        ];

        return Request::sendMessage($data);
    }
}
