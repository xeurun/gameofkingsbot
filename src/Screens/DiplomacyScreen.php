<?php

namespace App\Screens;

use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;

class DiplomacyScreen extends BaseScreen
{
    /**
     * {@inheritdoc}
     *
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): void
    {
        $title = ScreenInterface::SCREEN_DIPLOMACY;
        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_DIPLOMACY_SCREEN_MESSAGE,
            [
                '%title%' => $title,
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $keyboard = new Keyboard(
            ['text' => '🔜 Войны'],
            ['text' => '🔜 Союзы'],
            ['text' => ScreenInterface::SCREEN_EDICTS],
            ['text' => ScreenInterface::SCREEN_MAIN_MENU]
        );

        //Return a random keyboard.
        $keyboard = $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        $data = [
            'chat_id' => $this->botManager->getUser()->getId(),
            'text' => $text,
            'reply_markup' => $keyboard,
            'parse_mode' => 'Markdown',
        ];

        Request::sendMessage($data);
    }
}
