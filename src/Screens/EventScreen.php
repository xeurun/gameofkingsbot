<?php

namespace App\Screens;

use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;

class EventScreen extends BaseScreen
{
    /**
     * {@inheritdoc}
     *
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): void
    {
        $kingdom = $this->botManager->getKingdom();
        $title = ScreenInterface::SCREEN_EVENT;

        // Смотрим последнюю дату проверки событий и выводим
        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_EVENT_SCREEN_MESSAGE,
            [
                '%title%' => $title,
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $keyboard = new Keyboard(
            ['text' => '🔜 Аудиенция'],
            ['text' => '🔜 Охота'],
            ['text' => '🔜 Турнир'],
            ['text' => ScreenInterface::SCREEN_BACK]
        );

        //Return a random keyboard.
        $keyboard = $keyboard
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(false)
            ->setSelective(false);

        $data = [
            'chat_id' => $kingdom->getUser()->getId(),
            'text' => $text,
            'reply_markup' => $keyboard,
            'parse_mode' => 'Markdown',
        ];

        Request::sendMessage($data);
    }
}
