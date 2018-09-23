<?php

namespace App\Screens;

use App\Interfaces\ScreenInterface;
use App\Interfaces\TranslatorInterface;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

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
        $text = $this->botManager->getTranslator()->trans(
            TranslatorInterface::TRANSLATOR_MESSAGE_ACHIVEMENTS_SCREEN_MESSAGE,
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

        return Request::sendMessage($data);
    }
}
