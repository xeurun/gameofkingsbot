<?php

namespace App\Screens;

use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use App\Manager\BotManager;
use App\Manager\PeopleManager;
use App\Manager\WorkManager;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class EventScreen extends BaseScreen
{
    /**
     * @inheritdoc
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
                '%title%' => $title
            ],
            TranslatorInterface::TRANSLATOR_DOMAIN_SCREEN
        );

        $data = [
            'chat_id' => $kingdom->getUser()->getId(),
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        Request::sendMessage($data);
    }
}
